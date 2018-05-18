<template lang="pug">
  Panel.panel
    Filters(slot='header')
    Cells(slot='section')
</template>

<script>
  import Cells from './Cells'
  import Filters from './filters'

  export default {
    components: {
      Cells,
      Filters
    }
  }
</script>

<style lang="scss" scoped>
  /* Form Style */
</style>
