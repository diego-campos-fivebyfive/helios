<template lang="pug">
  aside.sidebar(:class='sidebarType')
    transition(name='fade')
      .sidebar-cover(
        class='modal-cover',
        v-show='getStateTwigModal()',
        v-on:click='toggleTwigModal')
    nav.menu
      .toogle
        .toggle-cover(
          class='modal-cover',
          v-show='getStateTwigModal()',
          v-on:click='toggleTwigModal')
        Button.toogle-button(
          class='primary-common',
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
      handleTwigModal: {
        type: Object,
        required: true
      },
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
      toggleTwigModal() {
        return this.handleTwigModal.toogle
      },
      getStateTwigModal() {
        return this.handleTwigModal.state
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
    z-index: 200;

    &.collapse {
      max-width: $ui-sidebar-collapse-x;
    }

    &.common {
      max-width: $ui-sidebar-common-x;
    }

    .menu {
      overflow-x: hidden;
    }
  }

  .modal-cover {
    background-color: rgba(0, 0, 0, 0.5);
    height: 100%;
    left: 0;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 250;
  }

  .toogle {
    position: absolute;
    right: -($ui-sidebar-toogle-x + $ui-space-x);
    top: $ui-space-y;
  }

  .fade-enter-active,
  .fade-leave-active {
    transition: all 150ms ease;
  }

  .fade-enter,
  .fade-leave-to {
    opacity: 0;
  }
</style>
