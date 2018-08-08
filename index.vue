<template lang="pug">
  .app-page(:class='sidebarType')
    FrameModal(:stateTwigModal='stateTwigModal')
    Sidebar(
      v-if='showSidebar()',
      :sidebarType='sidebarType',
      :updateSidebarType='updateSidebarType')
    main.app-page-main
      Mainbar(v-if='showMainbar()')
      .app-page-main-wrapper
        router-view
</template>

<script>
  import FrameModal from 'theme/template/frame-modal'
  import Mainbar from 'theme/template/mainbar'
  import Sidebar from 'theme/template/sidebar'

  export default {
    components: {
      FrameModal,
      Mainbar,
      Sidebar
    },
    data: () => ({
      stateTwigModal: false,
      mainbarType: '',
      sidebarType: '',
      stateSidebarType: 'common'
    }),
    watch: {
      $route() {
        this.setInitialSidebarType()
      }
    },
    mounted() {
      this.setInitialSidebarType()

      window.hideTwigModal = state => {
        this.stateTwigModal = state
      }

      window.updateSidebarType = sidebarType => {
        this.sidebarType = sidebarType
      }

      window.updateVueRoute = path => {
        // DEBUG: console.log('twig', location.pathname, path)

        setTimeout(() => {
          this.$route.meta.pushState = path
          history.replaceState({}, null, path)
        }, 1000)
      }
    },
    methods: {
      setInitialSidebarType() {
        this.mainbarType = this.$route.meta.mainbar || 'common'
        this.sidebarType = this.$route.meta.sidebar || this.stateSidebarType
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
    width: 100%;
  }
</style>
