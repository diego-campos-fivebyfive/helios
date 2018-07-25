<template lang="pug">
  .app-page(:class='sidebarType')
    Sidebar(
      v-if='showSidebar()',
      :sidebarType='sidebarType',
      :updateSidebarType='updateSidebarType',
      :handleTwigModal='handleTwigModal')
    main.app-page-main
      Mainbar(
        v-if='showMainbar()',
        :handleTwigModal='handleTwigModal')
      .app-page-main-wrapper
        router-view
</template>

<script>
  import Sidebar from 'theme/template/sidebar'
  import Mainbar from 'theme/template/mainbar'

  export default {
    components: {
      Sidebar,
      Mainbar
    },
    data: () => ({
      sidebarType: '',
      mainbarType: '',
      stateSidebarType: 'common',

      handleTwigModal: {
        state: false,
        toogle: () => {}
      }
    }),
    methods: {
      handleTwigModalOnWindow() {
        window.handleTwigModal = handler => {
          this.handleTwigModal = handler
        }
      },
      setDefaultSidebarType() {
        this.sidebarType = this.$route.meta.sidebar || this.stateSidebarType
        this.mainbarType = this.$route.meta.mainbar || 'common'
      },
      showSidebar() {
        return this.sidebarType !== "none"
      },
      showMainbar() {
        return this.mainbarType !== "none"
      },
      updateSidebarType() {
        this.sidebarType = (this.sidebarType === 'collapse')
          ? 'common'
          : 'collapse'

        this.stateSidebarType = this.sidebarType
      },
      updateVueRouteOnWindow() {
        window.updateVueRoute = path => {
          this.$router.push({ path })
        }
      }
    },
    mounted() {
      this.setDefaultSidebarType()
      this.updateVueRouteOnWindow()
      this.handleTwigModalOnWindow()
    },
    watch: {
      $route() {
        this.setDefaultSidebarType()
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
