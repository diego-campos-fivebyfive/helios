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
  import Sidebar from '@/theme/template/sidebar'
  import Mainbar from '@/theme/template/mainbar'

  export default {
    components: {
      Sidebar,
      Mainbar
    },
    data: () => ({
      sidebarType: '',
      mainbarType: '',
      handleTwigModal: {
        state: false,
        toogle: () => {}
      }
    }),
    methods: {
      updateSidebarType() {
        this.sidebarType = (this.sidebarType === 'collapse')
          ? 'common'
          : 'collapse'
      },
      setDefaultSidebarType() {
        this.sidebarType = this.$route.meta.sidebar || 'common'
        this.mainbarType = this.$route.meta.mainbar || 'common'
      },
      showSidebar() {
        return this.sidebarType !== "none"
      },
      showMainbar() {
        return this.mainbarType !== "none"
      }
    },
    mounted() {
      this.setDefaultSidebarType()

      window.updateVueRoute = path => {
        this.$router.push({ path })
      }

      window.handleTwigModal = handler => {
        this.handleTwigModal = handler
      }
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
