<template lang="pug">
  .collection-paginator
    nav(v-if='showPagination()')
      ul
        li(
          v-for='item in getNavigationItems()',
          :class='{ "collection-paginator-current": item.current }')
          span(v-if='!Number(item.value)')
            | {{ item.label }}
          router-link(v-else, :to='item.value')
            | {{ item.label }}
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
      showPagination() {
        return this.pagination.total
      },
      getRangeItems() {
        let ranges = []

        for(
          let i = 1;
          i <= this.pagination.total
            && ranges.length < 5;
          i++
        ) {
          ranges.push({
            label: i,
            value: i,
            current: this.getCurrent(i) 
          })
        }

        return ranges
      },
      getNavigationItems() {
        const navigationItems = this.getRangeItems()

        if (this.pagination.total > 5) {
          navigationItems.push(
            { label: '...' },
            {
              label: this.pagination.total,
              value: this.pagination.total
            }
          )
        }

        if (this.pagination.current || 1 < this.pagination.total) {
          navigationItems.push({ label: 'Próximo' })
        }

        return navigationItems
      },
      getCurrent(i) {
        if (!this.pagination.current && i === 1) {
          return true
        }

        return this.pagination.current === i
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
