<template lang="pug">
  Table.table(type='stripped')
    tr(slot='head')
      th.col-name Título
      th.col-code Url
      th.col-account Publicação
      th.col-action Ações
    tr.rows(slot='rows', v-for='term in terms')
      td.col-name {{ term.title }}
      td.col-code {{ term.url }}
      td.col-account {{ term.publishedAt }}
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
    ]
  }
</script>

<style lang="scss" scoped>
  .rows {
    cursor: pointer;
  }

  .col-name {
    min-width: 20%;
    text-align: left;
  }

  .col-code {
    text-align: center;
  }

  .col-account {
    min-width: 20%;
    text-align: center;
  }

  .col-status {
    text-align: center;
  }

  .col-amount {
    text-align: right;
  }

  .col-action {
    text-align: right;
  }
</style>
