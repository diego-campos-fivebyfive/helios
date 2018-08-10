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
            current: {
                type: Number,
                required: false,
                default: 1
            },
            pagination: {
                type: Object,
                required: false
            },
            total: {
                type: Number,
                required: false,
                default: 1
            }
        },
        data: () => ({
        params: {
            current: 1,
            total: 1
        }
    }),
    watch: {
        current: {
            handler: 'handleParams',
                immediate: true
        },
        pagination: {
            handler: 'handleParams',
                immediate: true
        },
        total: {
            handler: 'handleParams',
                immediate: true
        }
    },
    methods: {
        getCurrent(rangeIndex) {
            if (!this.params.current && rangeIndex === 1) {
                return true
            }

            return this.params.current === rangeIndex
        },
        getInitialRangeIndex() {
            if (
                this.params.current > 2
                && this.params.total >= 5
            ) {
                if (this.params.current + 2 > this.params.total) {
                    return this.params.total - 4
                }

                return this.params.current - 2
            }

            return 1
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
        },
        getNextControls(rangeItems) {
            const lastRangeItemIndex = rangeItems.length - 1
            const lastRangeItem = rangeItems[lastRangeItemIndex]

            const nextControls = []

            if (
                lastRangeItem.value !== this.params.total
                && this.params.total > 5
            ) {
                nextControls.push(
                    {
                        label: '...',
                        value: null
                    },
                    {
                        label: this.params.total,
                        value: this.params.total
                    }
                )
            }

            if (this.params.current < this.params.total) {
                nextControls.push({
                    label: 'Próximo',
                    value: this.params.current + 1
                })
            }

            return nextControls
        },
        getPrevControls(rangeItems) {
            const [firstRangeItem] = rangeItems

            const prevControls = []

            if (this.params.current > 1) {
                prevControls.push({
                    label: 'Anterior',
                    value: this.params.current - 1
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
        getRangeItems() {
            let ranges = []

            for(
                let i = this.getInitialRangeIndex();
                i <= this.params.total
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
        handleParams() {
            if (this.pagination && this.pagination.total) {
                this.params.total = this.pagination.total
                this.params.current = this.pagination.current || 1
                return
            }

            if (this.total) {
                this.params.total = this.total
                this.params.current = this.current
                return
            }

            throw new Error(`
          You must provide a total,
          as an arg for pagination prop
          or as a total prop
        `)
        },
        paginate(item) {
            if (item.value) {
                this.$emit('paginate', item.value)
            }
        },
        showPagination() {
            return this.params.total
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
    padding: $ui-space-y / 3 $ui-space-x / 2.5;

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
