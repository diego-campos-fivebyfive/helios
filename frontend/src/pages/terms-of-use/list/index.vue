<template lang="pug">
  div
    Confirm(ref='confirm', v-on:removeItem='removeTerm')
      div(slot='content')
        Icon.icon(name='question-circle-o', scale='4')
        h2.title Confirma exclusão deste Termo?
    Table.table(type='stripped')
      tr(slot='head')
        th.col-title Título
        th.col-url Url
        th.col-published Publicação
        th.col-action Ações
      tr.rows(slot='rows', v-for='term in terms')
        td.col-title {{ term.title }}
        td.col-url
          a(:href='term.url') {{ term.url }}
        td.col-published {{ formatDate(term.publishedAt) }}
        td.col-action
          Button(
            class='primary-common',
            pos='first',
            :action='() => $emit("show", "edit", term)')
            Icon(name='pencil')
          Button(
            class='danger-common',
            pos='last',
            :action='() => $refs.confirm.show(term.id)')
            Icon(name='trash')
</template>

<script>
  export default {
    props: [
      'terms',
      'notification'
    ],
    methods: {
      formatDate(date) {
        const moment = this.$moment(date, 'YYYY-MM-DD, hh:mm a')
        return moment.format('DD/MM/YYYY, hh:mm a')
      },
      removeTerm(id) {
        this.$refs.confirm.hide()

        this.axios.delete(`/admin/api/v1/terms/${id}`)
          .then(() => {
            this.$emit('getTerms')
            this.notification.notify('Termo removido com sucesso')
          })
      }
    }
  }
</script>

<style lang="scss" scoped>
  .rows {
    cursor: pointer;
  }

  .col-title {
    min-width: 30%;
    text-align: left;
  }

  .col-url {
    min-width: 30%;
    text-align: center;
  }

  .col-published {
    min-width: 30%;
    text-align: center;
  }

  .col-action {
    min-width: 10%;
    text-align: right;
  }

  a {
    color: $ui-blue-dark;

    &:hover {
      color: $ui-blue-darken;
    }
  }
</style>
