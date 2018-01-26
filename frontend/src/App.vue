<template lang="pug">
  .page
    Sidebar
    main.main
      router-view
</template>

<script>
  import Sidebar from '@/components/sidebar'

  export default {
    name: 'App',
    components: {
      Sidebar
    }
  }
</script>

<style lang="scss">
  body,
  html {
    background-color: $ui-gray-lighter;
    font-family: "Open Sans", "Helvetica Neue", sans-serif;
    font-size: 13px;
  }

  .page {
    min-height: 100vh;
    position: relative;
  }

  .main {
    @include clearfix;
    padding-left: $ui-sidebar-x;
  }
</style>
