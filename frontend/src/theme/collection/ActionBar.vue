<template lang="pug">
  .collection-action-bar
    Button.collection-action-bar-left(
      v-for='button in buttons.left',
      v-on:click.native='button.click',
      :key='button.icon',
      :icon='button.icon',
      :label='button.label || ""',
      :pos='button.position',
      type='default-bordered')
    Button.collection-action-bar-right(
      v-for='button in buttons.right',
      v-on:click.native='button.click',
      :key='button.icon',
      :icon='button.icon',
      :label='button.label || ""',
      :pos='button.position',
      type='default-bordered')
</template>

<script>
  export default {
    props: [
      'getMessages',
      'pagination'
    ],
    data() {
      const self = this

      return {
        buttons: {
          left: [{
            icon: 'refresh',
            position: 'single',
            label: 'atualizar',
            click: () => self.refresh()
          }, {
            icon: 'eye',
            position: 'single'
          }],
          right: [{
            icon: 'arrow-right',
            position: 'last',
            click: () => self.next()
          }, {
            icon: 'arrow-left',
            position: 'first',
            click: () => self.prev()
          }]
        }
      }
    },
    methods: {
      refresh() {
        this.getMessages(this.pagination.current)
      },
      next() {
        if (this.pagination.links.next) {
          const pageNumber = this.pagination.current + 1
          this.getMessages(pageNumber)
        }
      },
      prev() {
        if (this.pagination.links.prev) {
          const pageNumber = this.pagination.current - 1
          this.getMessages(pageNumber)
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  .collection-action-bar {
    width: 100%;

    .collection-action-bar-left {
      float: left;
      margin-right: 0.25rem;
    }

    .collection-action-bar-right {
      float: right;
    }
  }
</style>
