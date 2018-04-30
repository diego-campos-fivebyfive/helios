<template lang="pug">
  div
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
            type='primary-common',
            icon='pencil',
            pos='first')
          Button(
            type='danger-common',
            icon='trash',
            pos='last')
</template>

<script>
  export default {
    props: [
      'terms'
    ],
    methods: {
      formatDate(date) {
        const moment = this.$moment(date, 'YYYY-MM-DD, hh:mm a')
        return moment.format('DD/MM/YYYY, hh:mm a')
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
