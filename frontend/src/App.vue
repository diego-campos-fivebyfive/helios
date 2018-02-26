<template lang="pug">
  .app-page(:class='sidebar')
    Sidebar(v-if='sidebar !== "none"')
    main.app-page-main
      Mainbar(v-if='mainbar !== "none"')
      router-view
</template>

<script>
  import Sidebar from '@/components/template/sidebar'
  import Mainbar from '@/components/template/mainbar'

  export default {
    name: 'App',
    components: {
      Sidebar,
      Mainbar
    },
    computed: {
      sidebar() {
        return this.$route.meta.sidebar || 'common'
      },
      mainbar() {
        return this.$route.meta.mainbar || 'common'
      }
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

  .app-page {
    max-width: 100%;
    min-height: 100vh;
    position: relative;

    &.common {
      padding-left: $ui-sidebar-x;
    }
  }

  .app-page-main {
    @include clearfix;
  }
</style>
