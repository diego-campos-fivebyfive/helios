<template lang="pug">
  .page(:class='sidebar')
    Sidebar(v-if='showSidebar')
    main.main
      Mainbar(v-if='showMainbar')
      slot
</template>

<script>
  import Sidebar from '@/components/sidebar'
  import Mainbar from '@/components/mainbar'

  export default {
    props: [
      'sidebar',
      'mainbar'
    ],
    components: {
      Sidebar,
      Mainbar
    },
    computed: {
      showSidebar() {
        return (this.sidebar !== 'none')
      },
      showMainbar() {
        return (this.mainbar !== 'none')
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
