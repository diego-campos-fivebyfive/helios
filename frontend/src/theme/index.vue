<template lang="pug">
  .app-page(:class='sidebarType')
    Sidebar(
      v-if='sidebarType !== "none"',
      :sidebarType='sidebarType',
      :updateSidebarType='updateSidebarType')
    main.app-page-main
      Mainbar(v-if='mainbarType !== "none"')
      .app-page-main-wrapper
        router-view
</template>

<script>
  import Sidebar from '@/theme/template/sidebar'
  import Mainbar from '@/theme/template/mainbar'

  export default {
    components: {
      Sidebar,
      Mainbar
    },
    data: () => ({
      sidebarType: 'common',
      mainbarType: 'common'
    }),
    methods: {
      updateSidebarType() {
        this.sidebarType = (this.sidebarType === 'collapse')
          ? 'common'
          : 'collapse'
      }
    },
    mounted() {
      this.sidebarType = this.$route.meta.sidebar || this.sidebarType
      this.mainbarType = this.$route.meta.mainbar || this.mainbarType
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
      padding-left: $ui-sidebar-common-x;
    }

    &.collapse {
      padding-left: $ui-sidebar-collapse-x;
    }
  }

  .app-page-main {
    @include clearfix;
  }

  .app-page-main-wrapper {
    height: calc(100vh - #{$ui-mainbar-y});
    overflow-y: auto;
  }
</style>
