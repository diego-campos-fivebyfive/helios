<template lang="pug">
  .collection-paginator
    nav(v-if='showPagination()')
      button.collection-paginator-prev(
        v-if='previousPage()',
        v-on:click='paginate(pagination.current - 1)')
        | Anterior
      button.collection-paginator-number(
        v-for='pageNumber in pagination.total',
        v-on:click='paginate(pageNumber)',
        :class='{ "collection-paginator-current": isCurrent(pageNumber) }')
        | {{ pageNumber }}
      button.collection-paginator-next(
        v-if='nextPage()',
        v-on:click='paginate(pagination.current + 1)')
        | Próxima
</template>

<script>
  export default {
    props: {
      pagination: {
        type: Object,
        required: true
      }
    },
    methods: {
      paginate(pageNumber) {
        this.$emit('paginate', pageNumber)
      },
      isCurrent(pageNumber) {
        return this.pagination.current === pageNumber
      },
      showPagination() {
        return this.pagination.links && this.pagination.total > 1
      },
      nextPage() {
        return this.pagination.links.next
      },
      previousPage() {
        return this.pagination.links.prev
      }
    }
  }
</script>

<style lang="scss">
  .collection-paginator {
    text-align: center;

    nav {
      border: 1px solid $ui-gray-light;
      border-radius: $ui-corner;
      display: inline-block;
      margin-bottom: $ui-space-y;
    }
  }

  .collection-paginator-prev {
    border-right: 1px solid $ui-gray-light;

    &:before {
      content: "\AB";
      padding-right: $ui-space-x/5;
    }
  }

  .collection-paginator-next {
    border-left: 1px solid $ui-gray-light;

    &:after {
      content: "\BB";
      padding-left: $ui-space-x/5;
    }
  }

  .collection-paginator-current {
    background-color: $ui-gray-lighter;
  }

  .collection-paginator-number,
  .collection-paginator-prev,
  .collection-paginator-next {
    color: $ui-text-main;
    display: inline-block;
    padding: $ui-space-y/3 $ui-space-x/2.5;

    &:hover {
      background-color: $ui-gray-lighter;
    }
  }

  .collection-paginator-number:not(:first-child) {
    border-left: 1px solid $ui-gray-light;
  }
</style>