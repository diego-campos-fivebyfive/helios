<template lang="pug">
  .app-page(:class='sidebarTypes()')
    FrameModal(:twigModalState='twigModalState')
    Sidebar(
      :sidebarType='sidebarType',
      :updateSidebarType='updateSidebarType')
    main.app-page-main
      MainbarMobile(
        v-if='showMainbarMobile()',
        :updateSidebarType='updateSidebarType')
      Mainbar(v-if='showMainbar()')
      .app-page-main-wrapper(:class='sidebarTypes()')
        router-view
      TabBar(
        :tabs='tabs',
        v-if='showBottomBar()')
</template>

<script>
  import FrameModal from 'theme/template/frame-modal'
  import Mainbar from 'theme/template/mainbar'
  import TabBar from 'theme/template/tabbar'
  import MainbarMobile from 'theme/template/mainbar-mobile'
  import Sidebar from 'theme/template/sidebar'
  import tabs from '@/../theme/tabs'

  export default {
    name: 'App',
    components: {
      FrameModal,
      Mainbar,
      MainbarMobile,
      Sidebar,
      TabBar
    },
    data: () => ({
      twigModalState: false,
      mainbarType: '',
      sidebarType: '',
      stateSidebarType: 'common',
      mobileClass: process.env.PLATFORM !== 'web' ? 'mobile' : '',
      tabs
    }),
    watch: {
      $route: {
        handler: 'setInitialSidebarType',
        immediate: true
      }
    },
    mounted() {
      window.handleTwigModal = handler => {
        this.handleTwigModal = handler
      }

      window.updateSidebarType = sidebarType => {
        this.sidebarType = sidebarType
      }

      window.updateVueRoute = path => {
        // DEBUG: console.log('twig', location.pathname, path)

        if (process.env.PLATFORM !== 'web') {
          return
        }

        this.$route.meta.pushState = path
        history.replaceState({}, null, path)
      }

      window.pushVueRoute = fullPath => {
        const path = fullPath
          .replace(/\/twig/, '')
          .replace(/\/$/g, '')
          .replace(process.env.API_URL, '')

        this.$router.push({ path })
      }
    },
    methods: {
      setInitialSidebarType() {
        if (process.env.PLATFORM !== 'web') {
          this.mainbarType = 'none'
          this.sidebarType = 'none'
          return
        }

        this.mainbarType = this.$route.meta.mainbar || 'common'
        this.sidebarType = this.$route.meta.sidebar || this.stateSidebarType
      },
      showSidebar() {
        return this.sidebarType !== 'none'
      },
      showMainbar() {
        return this.mainbarType !== 'none' && process.env.PLATFORM === 'web'
      },
      showMainbarMobile() {
        return process.env.PLATFORM !== 'web'
      },
      showBottomBar() {
        return process.env.PLATFORM !== 'web'
      },
      updateSidebarType() {
        if (process.env.PLATFORM !== 'web') {
          this.sidebarType = (this.sidebarType === 'none')
            ? 'common'
            : 'none'
        }

        if (process.env.PLATFORM === 'web') {
          this.sidebarType = (this.sidebarType === 'collapse')
            ? 'common'
            : 'collapse'
        }

        this.stateSidebarType = this.sidebarType
      },
      sidebarTypes() {
        return `${this.sidebarType} ${this.mobileClass}`
      }
    }
  }
</script>

<style lang="scss">
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

    &.mobile {
      &.common {
        padding-left: 0;
      }
    }
  }

  .app-page-main {
    @include clearfix;
  }

  .app-page-main-wrapper {
    height: calc(100vh - #{$ui-mainbar-y});
    overflow-y: auto;
    width: 100%;

    &.mobile {
      padding-left: 0;
      margin-top: $ui-mainbar-mobile-y;
    }
  }
</style>
