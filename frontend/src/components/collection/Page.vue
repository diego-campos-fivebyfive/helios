<template lang="pug">
  .page
    Sidebar(v-if='sidebar')
    main.main
      slot
</template>

<script>
  import Sidebar from '@/components/sidebar'

  export default {
    props: [
      'sidebar'
    ],
    components: {
      Sidebar
    }
  }
</script>

<style lang="scss" scoped>
  .page {
    min-height: 100vh;
    position: relative;
  }

  .main {
    @include clearfix;
    padding-left: $ui-sidebar-x;
  }
</style>
