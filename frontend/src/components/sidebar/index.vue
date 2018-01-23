<template lang="pug">
  aside.sidebar
    nav
      Head
      Menu
</template>

<script>
  import Head from './Head'
  import Menu from './Menu'

  export default {
    components: {
      Head,
      Menu
    }
  }
</script>

<style lang="scss" scoped>
  $ui-bgcolor: #080c17;
  $ui-size-x: 220px;

  .sidebar {
    background-color: $ui-bgcolor;
    max-width: $ui-size-x;
  }
</style>
