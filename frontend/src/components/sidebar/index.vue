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
  $ui-gray-d: #080c17;
  $ui-size-x: 220px;

  .sidebar {
    background-color: $ui-gray-d;
    display: block;
    left: 0;
    max-width: $ui-size-x;
    min-height: 100%;
    position: absolute;
    top: 0;
    width: 100%;
  }
</style>
