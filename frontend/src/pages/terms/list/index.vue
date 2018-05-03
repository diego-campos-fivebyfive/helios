<template lang="pug">
  Table.table(type='stripped')
    tr(slot='head')
      th.col-title Título
      th.col-published Publicação
      th.col-action Aceitar
    tr.rows(slot='rows', v-for='term in terms')
      td.col-title
        a(:href='term.url', target='_blank') {{ term.title }}
      td.col-published {{ formatDate(term.publishedAt) }}
      td.col-action
        Checkbox(
          :field='includeCheckedState(term)',
          v-on:click.native='changedCheck(term, $event.target.checked)')
</template>

<script>
  import Checkbox from '@/theme/collection/Checkbox'

  export default {
    components: {
      Checkbox
    },
    props: [
      'terms',
      'notification'
    ],
    methods: {
      formatDate(date) {
        const moment = this.$moment(date, 'YYYY-MM-DD, hh:mm a')
        return moment.format('DD/MM/YYYY, hh:mm a')
      },
      includeCheckedState(term) {
        const checked = term.isAgree
        this.$set(term, 'value', Boolean(checked))

        return term
      },
      changedCheck(term, checked) {
        let uri = ''

        if (checked) {
          uri = `/api/v1/terms/agree/${term.id}`
        } else {
          uri = `/api/v1/terms/disagree/${term.id}`
        }

        this.axios.post(uri)
      }
    }
  }
</script>

<style lang="scss" scoped>
  .rows {
    cursor: pointer;
  }

  .col-title {
    width: 45%;
    text-align: left;
  }

  .col-published {
    width: 45%;
    text-align: left;
  }

  .col-action {
    width: 10%;
    text-align: center;
  }

  a {
    color: $ui-blue-dark;

    &:hover {
      color: $ui-blue-darken;
    }
  }
</style>
