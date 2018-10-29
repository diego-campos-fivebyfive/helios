<template lang="pug">
  .app-page(:class='[sidebarType, platform]')
    Sidebar(
      :sidebarType='sidebarType',
      :updateSidebarType='updateSidebarType')
    main.app-page-main
      MainbarMobile(
        v-if='showMainbarMobile()',
        :updateSidebarType='updateSidebarType')
      Mainbar(v-if='showMainbar()')
      .app-page-main-wrapper(:class='[sidebarType, platform]')
        router-view
      TabBar(
        v-if='showTabbar()',
        :tabs='tabs')
</template>

<script>
  import Mainbar from 'theme/template/mainbar'
  import TabBar from 'theme/template/tabbar'
  import MainbarMobile from 'theme/template/mainbar-mobile'
  import Sidebar from 'theme/template/sidebar'
  import tabs from '@/../theme/tabs'
console.log(tabs)
  export default {
    name: 'App',
    components: {
      Mainbar,
      MainbarMobile,
      Sidebar,
      TabBar
    },
    data: () => ({
      mainbarType: '',
      mobileMainbarType: '',
      tabbarType: '',
      sidebarType: '',
      stateSidebarType: 'common',
      platform: process.env.PLATFORM !== 'web' ? 'mobile' : 'web',
      tabs
    }),
    watch: {
      $route: {
        handler: 'setInitialComponents',
        immediate: true
      }
    },
    computed: {
      isMobile() {
        return process.env.PLATFORM !== 'web'
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
      setInitialComponents() {
        if (this.isMobile) {
          this.tabbarType = this.$route.meta.tabbar || 'common'
          this.mobileMainbarType = this.$route.meta.mainbar || 'common'
          this.mainbarType = 'none'
          this.sidebarType = 'none'
        } else {
          this.tabbarType = 'none'
          this.mobileMainbarType = 'none'
          this.mainbarType = this.$route.meta.mainbar || 'common'
          this.sidebarType = this.$route.meta.sidebar || this.stateSidebarType
        }
      },
      showMainbar() {
        return this.mainbarType !== 'none' && !this.isMobile
      },
      showMainbarMobile() {
        return this.mobileMainbarType !== 'none' && this.isMobile
      },
      showTabbar() {
        return this.tabbarType !== 'none'
          && this.isMobile
          && this.tabs.length > 0
      },
      updateSidebarType() {
        if (this.isMobile) {
          this.sidebarType = (this.sidebarType === 'none')
            ? 'common'
            : 'none'
        } else {
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
      height: calc(100vh - (#{$ui-mainbar-mobile-y} + #{$ui-tabbar-mobile-y}));
      margin-top: $ui-mainbar-mobile-y;
    }
  }
</style>
