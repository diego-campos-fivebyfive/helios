<template lang="pug">
  aside.sidebar(:class='sidebarType')
    nav
      Head(:sidebarType='sidebarType')
      Menu
</template>

<script>
  import Head from './Head'
  import Menu from './Menu'

  export default {
    components: {
      Head,
      Menu
    },
    computed: {
      sidebarType() {
        return this.$route.meta.sidebar || 'common'
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
    overflow: hidden;
    top: 0;
    width: 100%;
    z-index: 25;

    &.collapse {
      max-width: $ui-sidebar-collapse-x;
    }

    &.common {
      max-width: $ui-sidebar-common-x;
    }
  }
</style>
