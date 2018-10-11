<template lang="pug">
  transition(name='sidebar-slide')
    aside.sidebar(:class='sidebarType')
      transition(name='fade')
        .sidebar-cover(
          v-if='showSidebarCover()',
          v-on:click='updateSidebarType')
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
      transition: all 100ms ease-in-out;
    }

    .menu {
      overflow-x: hidden;
    }
  }

  .sidebar-cover {
    background: rgba(0, 0, 0, 0.35);
    height: 100%;
    left: 0;
    top: 0;
    position: fixed;
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
</style>
