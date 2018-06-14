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
  .sidebar {
    background-color: $ui-gray-darken;
    display: block;
    left: 0;
    max-width: $ui-sidebar-x;
    min-height: 100%;
    position: absolute;
    overflow: hidden;
    top: 0;
    width: 100%;
  }
</style>
