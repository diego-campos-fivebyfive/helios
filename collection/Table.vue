<template lang="pug">
  table.collection-table(:class='type')
    thead.collection-table-header
      slot(name='head')
    tbody.collection-table-section
      slot(name='rows')
</template>

<script>
  export default {
    props: {
      type: {
        type: String,
        required: false
      }
    }
  }
</script>

<style lang="scss">
  $table-striped-bg: #f9f9f9;
  $table-striped-bg_: #f5f5f5;
  $table-bordered-bg: #f5f5f5;

  %stripped {
    border-spacing: 0;

    .collection-table-header {
      text-align: left;
    }

    .collection-table-section {
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

  %bordered {
    border-spacing: 0;
    border-collapse: separate;

    .collection-table-header {
      background-color: $table-bordered-bg;
      border-color: inherit;
    }

    td,
    th {
      border: $ui-space-y/15 solid $ui-divider-color;
      padding: $ui-space-y/2;
      vertical-align: middle;
    }
  }

  .collection-table {
    display: table;
    width: 100%;

    &.stripped {
      @extend %stripped;
    }

    &.bordered {
      @extend %bordered;
    }
  }
</style>
