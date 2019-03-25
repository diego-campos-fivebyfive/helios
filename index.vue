<template lang="pug">
  .app-wrapper
    ConnectionAlert
    .app-page(:class='[sidebarType, platform]')
      Sidebar(
        :sidebarType='sidebarType',
        :updateSidebarType='updateSidebarType')
      transition(name='fade')
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
  import Mainbar from 'theme/template/mainbar/web'
  import TabBar from 'theme/template/tabbar'
  import MainbarMobile from 'theme/template/mainbar/mobile'
  import Sidebar from 'theme/template/sidebar'
  import ConnectionAlert from 'theme/template/connection-alert'
  import tabs from '@/../theme/tabs'
  import $locale from 'locale'
  import pullToRefresh from 'pulltorefreshjs'

  const App = {
    templateOptions: {
      el: '#app',
      template: '<App/>',
      render: h => h(App)
    },
    name: 'App',
    components: {
      Mainbar,
      MainbarMobile,
      Sidebar,
      TabBar,
      ConnectionAlert
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

      /**
       * @see https://stackoverflow.com/a/25098153
       */
      window.addEventListener('message', event => {
        /* eslint-disable no-bitwise */
        if (!~event.origin.indexOf(process.env.API_URL)) {
          return
        }

        const { data } = event
        const path = this.formatPath(data.path)

        if (path === this.formatPath(window.location.href)) {
          return
        }

        if (data.event === 'updateVueRoute') {
          if (this.isModal(data.searchParams)) {
            return
          }

          this.updateVueRoute(path)
          return
        }

        if (data.event === 'pushVueRoute') {
          this.pushVueRoute(path)
        }
      })

      this.downloadFileFromIframe()

      this.mobilePullRefresh()
    },
    methods: {
      formatPath(path) {
        return path
          .replace(/\/twig/, '')
          .replace(/\/$/g, '')
          .replace(process.env.API_URL, '')
          .replace(window.location.origin, '')
      },
      isModal(searchParams) {
        const urlParams = new URLSearchParams(searchParams)

        return urlParams.get('modal')
      },
      pushVueRoute(path) {
        this.$router.push({ path })
      },
      updateVueRoute(path) {
        // DEBUG: console.log('twig', location.pathname, path)
        this.$route.meta.pushState = path
        window.history.replaceState({}, null, path)
      },
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
          && this.tabs.length
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
      },
      downloadFileFromIframe() {
        const { addEventListener, attachEvent } = window

        const launchFile = message => {
          const { download } = message.data
          if (!download) {
            return false
          }

          const newWindow = process.env.PLATFORM !== 'web'
            ? 'location=yes'
            : null

          window.open(download, '_system', newWindow)
        }

 				addEventListener
					? addEventListener('message', launchFile, false)
					: attachEvent('onmessage', launchFile, false)
      },
      mobilePullRefresh() {
        pullToRefresh.init({
          mainElement: 'body',
          instructionsPullToRefresh: $locale.theme.template.pullToRefresh,
          instructionsRefreshing: $locale.theme.template.refreshing,
          instructionsReleaseToRefresh: $locale.theme.template.releaseToReload,
          onRefresh: () => {
            location.reload(true)
          },
          shouldPullToRefresh: () => {
            return !window.scrollY && this.sidebarType === 'none'
          }
        })
      }
    }
  }

  export default App
</script>

<style lang="scss">
  .app-page {
    max-width: 100%;
    min-height: 100vh;
    position: relative;

    &.common {
      padding-left: $ui-sidebar-common-x;
      transition: 0.2s;
    }

    &.collapse {
      padding-left: $ui-sidebar-collapse-x;
      transition: 0.2s;
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
