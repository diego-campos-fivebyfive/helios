<template lang="pug">
  div
    h2.title
      | Gerenciamento de Cupons
    nav.menu
      Button(
        type='primary-common',
        icon='plus-square',
        label='Novo Cupom',
        pos='single',
        v-on:click.native='$emit("show")')
</template>

<script>
  /* Head Script */
</script>

<style lang="scss" scoped>
  .title {
    text-align: center;
  }
</style>
