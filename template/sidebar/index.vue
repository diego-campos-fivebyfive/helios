<template lang="pug">
  transition(name='sidebar-slide')
    aside.sidebar(:class='[sidebarType, platform]')
      transition(name='fade')
        .sidebar-cover(
          v-show='showSidebarCover()',
          v-on:click='updateSidebarType')
      nav.menu(v-if='showMenu()',)
        .toogle
          Button.toogle-button(
            class='primary-common',
            v-if='!isMobile',
            :action='updateSidebarType')
            Icon(name='bars')
        Head(:sidebarType='sidebarType', v-if='!isMobile')
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
    data: () => ({
      platform: process.env.PLATFORM !== 'web' ? 'mobile' : ''
    }),
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
    computed: {
      isMobile() {
        return process.env.PLATFORM !== 'web'
      }
    },
    methods: {
      showSidebarCover() {
        return this.isMobile && this.sidebarType === 'common'
      },
      showMenu() {
        return this.sidebarType !== 'none'
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

    &.none {
      display: none;
    }

    .menu {
      overflow-x: hidden;
    }
  }

  .sidebar-cover {
    background: rgba(0, 0, 0, 0.35);
    height: 100%;
    left: 0;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: -1;
  }

  .toogle {
    position: absolute;
    right: -($ui-sidebar-toogle-x + $ui-space-x);
    top: $ui-space-y;
    z-index: 50;
  }

  .sidebar-slide-enter-active {
    position: absolute
  }

  .sidebar-slide-enter-active,
  .sidebar-slide-leave-active {
    transition: all 750ms ease-in-out
  }

  .mobile {
    &.none {
      display: block;
      max-width: $ui-sidebar-common-x;
      margin-left: -($ui-sidebar-common-x);
      padding-top: $ui-mainbar-mobile-y;
      transition: all 300ms ease-in-out;
    }

    &.common {
      margin-left: 0;
      padding-top: $ui-mainbar-mobile-y;
      transition: all 300ms ease-in-out;
    }
  }
</style>
