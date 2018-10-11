<template lang="pug">
  transition(name='slide')
    aside.sidebar(:class='sidebarType')
      transition(name='fade')
        .sidebar-cover(v-if='showSidebarCover()')
      nav.menu
        .toogle
          Button.toogle-button(
            class='primary-common',
            v-if='showMobileMainbar()',
            :action='updateSidebarType')
            Icon(name='bars')
        Head(:sidebarType='sidebarType')
        Menu(:sidebarType='sidebarType')
</template>

<script>
  import Head from './Head'
  import Menu from './Menu'

  export default {
    components: {
      Head,
      Menu
    },
    props: {
      sidebarType: {
        type: String,
        required: true
      },
      updateSidebarType: {
        type: Function,
        required: true
      }
    },
    watch: {
      sidebarType() {}
    },
    methods: {
      showMobileMainbar() {
        return process.env.PLATFORM === 'web'
      },
      showSidebarCover() {
        return this.sidebarType === 'mobile'
      }
    }
  }
</script>

<style lang="scss" scoped>
  .sidebar {
    background-color: $ui-gray-darken;
    display: block;
    left: 0;
    min-height: 100%;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 50;

    &.collapse {
      max-width: $ui-sidebar-collapse-x;
    }

    &.common {
      max-width: $ui-sidebar-common-x;
    }

    &.mobile {
      max-width: $ui-sidebar-common-x;
      margin-left: 0;
      margin-top: $ui-mainbar-mobile-y;
      transition: all 100ms ease-in-out;
    }

     &.occult {
      max-width: $ui-sidebar-common-x;
      margin-left: -($ui-sidebar-common-x);
      margin-top: $ui-mainbar-mobile-y;
      transition: all 300ms ease-in-out;
    }

    .menu {
      overflow-x: hidden;
    }
  }

  .sidebar-cover {
    top: 0;
    width: 100%;
    height: 100%;
    left: 0;
    position: fixed;
    background: rgba(0, 0, 0, 0.35);
    z-index: -1;
  }

  .toogle {
    position: absolute;
    right: -($ui-sidebar-toogle-x + $ui-space-x);
    top: $ui-space-y;
  }

  .slide-enter-active { position: absolute }
  .slide-enter-active,
  .slide-leave-active { transition: all 750ms ease-in-out }
</style>
