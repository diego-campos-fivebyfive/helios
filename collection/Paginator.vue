<template lang="pug">
  .collection-paginator
    nav.collection-paginator-wrapper(v-if='showPagination()')
      button.collection-paginator-item(
        v-for='item in getNavigationItems()',
        v-on:click='paginate(item)',
        :class='{ current: item.current }')
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
      paginate(item) {
        if (item.value) {
          this.$emit('paginate', item.value)
        }
      },
      getInitialRangeIndex() {
        if (
          this.pagination.current > 2
          && this.pagination.total >= 5
        ) {
          if (this.pagination.current + 2 > this.pagination.total) {
            return this.pagination.total - 4
          }

          return this.pagination.current - 2
        }

        return 1
      },
      getCurrent(rangeIndex) {
        if (!this.pagination.current && rangeIndex === 1) {
          return true
        }

        return this.pagination.current === rangeIndex
      },
      getRangeItems() {
        let ranges = []

        for(
          let i = this.getInitialRangeIndex();
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
      getPrevControls(rangeItems) {
        const [firstRangeItem] = rangeItems

        const prevControls = []

        if (this.pagination.current > 1) {
          prevControls.push({
            label: 'Anterior',
            value: this.pagination.current - 1
          })
        }

        if (firstRangeItem.value > 1) {
          prevControls.push(
            {
              label: 1,
              value: 1
            },
            {
              label: '...',
              value: null
            }
          )
        }

        return prevControls
      },
      getNextControls(rangeItems) {
        const lastRangeItemIndex = rangeItems.length - 1
        const lastRangeItem = rangeItems[lastRangeItemIndex]

        const nextControls = []

        if (
          lastRangeItem.value !== this.pagination.total
          && this.pagination.total > 5
        ) {
          nextControls.push(
            {
              label: '...',
              value: null
            },
            {
              label: this.pagination.total,
              value: this.pagination.total
            }
          )
        }

        if (this.pagination.current < this.pagination.total) {
           nextControls.push({
            label: 'Próximo',
            value: this.pagination.current + 1
          })
        }

        return nextControls
      },
      getNavigationItems() {
        const rangeItems = this.getRangeItems()
        const prevControls = this.getPrevControls(rangeItems)
        const nextControls = this.getNextControls(rangeItems)

        return [
          ...prevControls,
          ...rangeItems,
          ...nextControls
        ]
      }
    }
  }
</script>

<style lang="scss">
  .collection-paginator {
    text-align: center;
  }

  .collection-paginator-wrapper {
    border: 1px solid $ui-gray-light;
    border-radius: $ui-corner;
    display: inline-block;
    margin-bottom: $ui-space-y;
  }

  .collection-paginator-item {
    color: $ui-text-main;
    display: inline-block;
    padding: $ui-space-y/3 $ui-space-x/2.5;

    &:hover {
      background-color: $ui-gray-lighter;
    }

    &.current {
      background-color: $ui-gray-lighter;
    }

    &:not(:first-child) {
      border-left: 1px solid $ui-gray-light;
    }
  }
</style>
