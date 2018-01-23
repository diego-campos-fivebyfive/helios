<template lang="pug">
  main.main
    Sidebar
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

  body,
  html {
    font-family: "Open Sans", "Helvetica Neue", sans-serif;
    font-size: 13px;
  }
</style>
