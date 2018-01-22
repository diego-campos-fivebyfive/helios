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
  @import "~styles/generic-normalize.scss";
  @import "~styles/generic-reset.scss";

  body,
  html {
    font-family: "open sans","Helvetica Neue",Helvetica,Arial,sans-serif;
    font-size: 13px;
  }
</style>
