<template lang="pug">
  .app-page(:class='sidebarType')
    FrameModal(:twigModalState='twigModalState')
    Sidebar(
      v-if='showSidebar()',
      :sidebarType='sidebarType',
      :updateSidebarType='updateSidebarType')
    main.app-page-main
      MainbarMobile(
        v-if='showMainbarMobile()',
        :updateSidebarType='updateSidebarType')
      Mainbar(v-if='showMainbar()')
      .app-page-main-wrapper(:class='sidebarType')
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
      tabs: [{
          'title': 'My orders',
          'icon': 'sun-o',
          'to': '/orders'
        },{
          'title': 'Sices Express',
          'icon': 'cart-plus',
          'to': '/kit'
        },{
          'title': 'Dashboard',
          'icon': 'dashboard',
          'to': '/dashboard'
        },{
          'title': 'Ranking',
          'icon': 'trophy',
          'to': '/ranking'
        },{
          'title': 'Notifications',
          'icon': 'bell',
          'to': '/notification'
        }
      ]
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
          this.sidebarType = 'occult'
          return
        }

        this.mainbarType = this.$route.meta.mainbar || 'common'
        this.sidebarType = this.$route.meta.sidebar || this.stateSidebarType
      },
      showSidebar() {
        return this.sidebarType !== "none"
      },
      showMainbar() {
        return this.mainbarType !== "none" && process.env.PLATFORM === 'web'
      },
      showMainbarMobile() {
        return process.env.PLATFORM !== 'web'
      },
      showBottomBar() {
        return process.env.PLATFORM !== 'web'
      },
      updateSidebarType() {
        if (process.env.PLATFORM !== 'web') {
          this.sidebarType = (this.sidebarType === 'occult')
            ? 'mobile'
            : 'occult'
        }

        if (process.env.PLATFORM === 'web') {
          this.sidebarType = (this.sidebarType === 'collapse')
            ? 'common'
            : 'collapse'
        }

        this.stateSidebarType = this.sidebarType
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
  }

  .app-page-main {
    @include clearfix;
  }

  .app-page-main-wrapper {
    height: calc(100vh - #{$ui-mainbar-y});
    overflow-y: auto;
    width: 100%;

    &.mobile, &.occult {
      margin-top: $ui-mainbar-mobile-y;
    }
  }
</style>
