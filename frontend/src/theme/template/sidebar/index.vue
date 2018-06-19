<template lang="pug">
  aside.sidebar(:class='sidebarType')
    nav
      Button.toogle(
        class='primary-common',
        :action='updateSidebarType')
        Icon(name='bars')
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
    data: () => ({
      sidebarType: ''
    }),
    methods: {
      updateSidebarType() {
        this.sidebarType = (this.sidebarType === 'collapse')
          ? 'common'
          : 'collapse'
      }
    },
    mounted() {
      this.sidebarType = this.$route.meta.sidebar || 'common'
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
