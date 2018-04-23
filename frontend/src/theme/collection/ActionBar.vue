<template lang="pug">
  .collection-action-bar
    Button.collection-action-bar-left(
      type='default-bordered',
      icon='refresh',
      label='Atualizar',
      pos='single')
    Button.collection-action-bar-left(
      type='default-bordered',
      icon='eye',
      pos='single')
    Button.collection-action-bar-left(
      type='default-bordered',
      icon='exclamation',
      pos='single')
    Button.collection-action-bar-left(
      type='default-bordered',
      icon='trash',
      pos='single')
    Button.collection-action-bar-right(
      type='default-bordered',
      icon='arrow-left',
      pos='last')
    Button.collection-action-bar-right(
      type='default-bordered',
      icon='arrow-right',
      pos='first')
</template>

<script>
  /* ActionBar Script */
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
