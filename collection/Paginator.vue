<template lang="pug">
  .collection-paginator
    nav(v-if='showPagination()')
      button.collection-paginator(
        v-for='item in getNavigationItems()',
        v-on:click='paginate(item)',
        :class='{ "collection-paginator-current": item.current }')
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
      paginate(item) {
        if (Number(item.value)) {
          this.$emit('paginate', item.value)
        }
      },
      showPagination() {
        return this.pagination.total
      },
      getInitialRangeIndex() {
        if (
          this.pagination.current > 2
          && this.pagination.total >= 5
        ) {
          if (
            this.pagination.current + 2
            > this.pagination.total
          ) {
            return this.pagination.total - 4
          }

          return this.pagination.current - 2
        }

        return 1
      },
      getRangeItems() {
        let ranges = []

        let i = this.getInitialRangeIndex()

        for(;
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
        let navigationItems = []
        const rangeItems = this.getRangeItems()
        const [firstRangeItem] = rangeItems

        if (this.pagination.current > 1) {
          navigationItems.push({
            label: 'Anterior',
            value: this.pagination.current - 1
          })
        }

        if (firstRangeItem.value > 1) {
          navigationItems.push(
            {
              label: 1,
              value: 1
            },
            { label: '...' }
          )
        }

        navigationItems = navigationItems.concat(rangeItems)

        if (this.pagination.total > 5) {
          navigationItems.push(
            { label: '...' },
            {
              label: this.pagination.total,
              value: this.pagination.total
            }
          )
        }

        if (this.pagination.current < this.pagination.total) {
          navigationItems.push({
            label: 'Próximo',
            value: this.pagination.current + 1
          })
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
