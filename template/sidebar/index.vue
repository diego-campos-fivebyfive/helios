<template lang="pug">
  transition(name='sidebar-slide')
    aside.sidebar(:class='[sidebarType, platform]')
      transition(name='fade')
        .backdrop(
          v-show='showSidebarCover()',
          v-on:click='updateSidebarType')
      transition(name='slide-fade')
        nav.menu(v-if='showMenu()')
          .toogle
            Button.toogle-button(
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

  .backdrop {
    background: rgba(0, 0, 0, 0.5);
    height: 100vh;
    left: $ui-sidebar-common-x;
    position: fixed;
    width: 100vw;
    z-index: -1;
  }

  .toogle {
    position: absolute;
    right: -($ui-sidebar-toogle-x + ($ui-space-x / 2));
    top: $ui-space-y / 2;
    z-index: 50;

    .toogle-button {
      color:  $ui-gray-regular;
    }
  }

  .sidebar-slide-enter-active {
    position: absolute
  }

  .mobile {
    background: $ui-white-regular;
    padding-top: $ui-mainbar-mobile-y;

    &.none {
      display: block;
      max-width: $ui-sidebar-common-x;
      transform: translateX(-100%);
    }

    &.common {
			transform: none;
	    transition: transform 200ms linear;
    }
  }

  .toogle:hover {
    opacity: 0.5;
    transition: 1s;
  }

  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.6s;
  }

  .fade-enter, .fade-leave-to {
    opacity: 0;
  }
</style>
