<template lang="pug">
  aside.sidebar(:class='sidebarType')
    nav
      Button.toogle(
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
    overflow: hidden;
    top: 0;
    width: 100%;
    z-index: 100;

    &.collapse {
      max-width: $ui-sidebar-collapse-x;
    }

    &.common {
      max-width: $ui-sidebar-common-x;
    }
  }

  .toogle {
    position: absolute;
    right: -($ui-sidebar-toogle-x + $ui-space-x);
    top: $ui-space-y;
  }
</style>
