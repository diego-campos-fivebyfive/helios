<template lang="pug">
  .colletion-page(:class='sidebar')
    Sidebar(v-if='showSidebar')
    main.colletion-page-main
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

<style lang="scss">
  .colletion-page {
    max-width: 100%;
    min-height: 100vh;
    position: relative;

    &.common {
      padding-left: $ui-sidebar-x;
    }
  }

  .colletion-page-main {
    @include clearfix;
  }
</style>
