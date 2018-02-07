<template lang="pug">
  table(:class='type')
    thead
      slot(name='head')
    tbody
      slot(name='rows')
</template>

<script>
  export default {
    props: ['type']
  }
</script>

<style lang="scss" scoped>
  $table-striped-bg: #f9f9f9;
  $table-striped-bg_: #f5f5f5;

  table {
    display: table;
    width: 100%;
  }

  .striped {
    border-spacing: 0;

    thead {
      text-align: left;
    }

    tbody {
      tr {
        &:hover {
          background-color: $table-striped-bg_;
        }

        &:nth-child(odd) {
          background-color: $table-striped-bg;
        }
      }
    }

    td,
    th {
      padding: $ui-space-y/2;
      vertical-align: top;
    }

    td {
      border-top: $ui-space-y/10 solid $ui-divider-color;
    }
  }
</style>
