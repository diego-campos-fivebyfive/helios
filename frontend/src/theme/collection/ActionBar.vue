<template lang="pug">
  .collection-action-bar
    Button.collection-action-bar-left(
      v-for='button in buttons.left',
      :key='button.icon',
      :icon='button.icon',
      :label='button.label || ""',
      :pos='button.position',
      type='default-bordered')
    Button.collection-action-bar-right(
      v-for='button in buttons.right',
      :key='button.icon',
      :icon='button.icon',
      :label='button.label || ""',
      :pos='button.position',
      type='default-bordered')
</template>

<script>
  export default {
    data: () => ({
      buttons: {
        left: [
          { icon: 'refresh', position: 'single', label: 'atualizar' },
          { icon: 'eye', position: 'single' }
        ],
        right: [
          { icon: 'arrow-right', position: 'last' },
          { icon: 'arrow-left', position: 'first' }
        ]
      }
    })
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
