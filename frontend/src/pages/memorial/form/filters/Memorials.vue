<template lang="pug">
  Button(
    type='default-bordered'
    icon='arrow-left',
    label='voltar',
    pos='single')
  Select(
    :options='options',
    :selected='getCurrentAgent',)
</template>

<script>
  export default {

  }
</script>

<style lang="scss" scoped>
  /* Head */
</style>
