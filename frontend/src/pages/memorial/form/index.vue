<template lang="pug">
  Panel.panel
    Filters(slot='header')
</template>

<script>
  import Filters from './filters'

  export default {
    components: {
      Filters
    }
  }
</script>

<style lang="scss" scoped>
  /* Form Style */
</style>
