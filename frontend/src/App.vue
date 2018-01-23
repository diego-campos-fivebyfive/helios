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
  @import "~styles/setting-font.scss";
  @import "~styles/generic-normalize.scss";
  @import "~styles/generic-reset.scss";

  $ui-white-d: #f3f3f4;
  $ui-sidebar-x: 220px;

  body,
  html {
    background-color: $ui-white-d;
    font-family: "Open Sans", "Helvetica Neue", sans-serif;
    font-size: 13px;
  }

  .main {
    padding-left: $ui-sidebar-x;
  }
</style>
