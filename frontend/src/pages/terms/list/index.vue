<template lang="pug">
  Table.table(type='stripped')
    tr(slot='head')
      th.col-title Título
      th.col-published Publicação
      th.col-action Status
    tr.rows(slot='rows', v-for='term in terms')
      td.col-title
        a(:href='term.url', target='_blank') {{ term.title }}
      td.col-published {{ formatDate(term.publishedAt) }}
      td.col-action
        Button(
          :class='[{ "button-active": term.isAgree }, "default-bordered"]',
          label='Aceito',
          pos='first',
          :action='() => accept(term.id)')
        Button(
          :class='[{ "button-active": !term.isAgree }, "default-bordered"]',
          label='Não Aceito',
          pos='last',
          :action='() => noAccept(term.id)')
</template>

<script>
  export default {
    props: {
      notification: {
        type: Object,
        required: false
      },
      pagination: {
        type: Object,
        required: true
      },
      terms: {
        type: Array,
        required: true
      }
    },
    methods: {
      formatDate(date) {
        const moment = this.$moment(date, 'YYYY-MM-DD, hh:mm a')
        return moment.format('DD/MM/YYYY, hh:mm a')
      },
      accept(termId) {
        const uri = `/api/v1/terms/agree/${termId}`

        this.axios.post(uri).then(() => {
          this.$emit('getTerms', this.pagination.current)
        })
      },
      noAccept(termId) {
        const uri = `/api/v1/terms/disagree/${termId}`

        this.axios.post(uri).then(() => {
          this.$emit('getTerms', this.pagination.current)
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
    width: 40%;
    text-align: left;
  }

  .col-published {
    width: 40%;
    text-align: left;
  }

  .col-action {
    width: 20%;
    text-align: center;
  }

  .button-active {
    background-color: $ui-blue-dark !important;
    border-color: $ui-blue-dark !important;
    box-shadow:
      inset 4px 4px 6px $ui-blue-darken,
      inset -4px -4px 6px $ui-blue-darken;
    color: $ui-white-regular !important;

    &:hover {
      background-color: $ui-blue-dark !important;
      border-color: $ui-blue-dark !important;
      color: $ui-white-regular !important;
    }

    &:active {
      background-color: $ui-blue-dark !important;
      border-color: $ui-blue-dark !important;
      box-shadow:
        inset 4px 4px 6px $ui-blue-darken,
        inset -4px -4px 6px $ui-blue-darken;
      color: $ui-white-regular !important;
    }
  }

  a {
    color: $ui-blue-dark;

    &:hover {
      color: $ui-blue-darken;
    }
  }
</style>
