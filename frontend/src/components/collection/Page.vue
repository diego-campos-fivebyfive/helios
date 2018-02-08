<template lang="pug">
  .page(:class='sidebar')
    Sidebar(v-if='showSidebar')
    main.main
      Mainbar
      slot
</template>

<script>
  import Sidebar from '@/components/sidebar'
  import Mainbar from '@/components/mainbar'

  export default {
    props: [
      'sidebar'
    ],
    components: {
      Sidebar,
      Mainbar
    },
    computed: {
      showSidebar() {
        return (this.sidebar !== 'none')
      }
    }
  }
</script>

<style lang="scss" scoped>
  .page {
    max-width: 100%;
    min-height: 100vh;
    position: relative;

    &.common {
      padding-left: $ui-sidebar-x;
    }
  }

  .main {
    @include clearfix;
  }
</style>
