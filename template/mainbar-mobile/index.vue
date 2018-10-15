<template lang="pug">
  header.mainbar-mobile
    ul.mainbar-mobile-list
      li.mainbar-mobile-list-button-left
        Button.mainbar-mobile-list-toggle-sidebar-left(
          :action='updateSidebarType')
          Icon(name='list')
      li.mainbar-mobile-list-title
        | {{ pageTitle }}
      li.mainbar-mobile-list-button-right
        Button.mainbar-mobile-list-toggle-sidebar-right(
          link='/logout')
          Icon(name='sign-out')
</template>

<script>
  export default {
    data: () => ({
      pageTitle: ''
    }),
    props: {
      updateSidebarType: {
        type: Function,
        required: true
      }
    },
    watch: {
      $route: {
        handler: 'setPageTitle',
        immediate: true
      }
    },
    methods: {
      setPageTitle() {
        this.pageTitle = this.$router.history.current.meta.title
      }
    }
  }
</script>

<style lang="scss" scoped>
  .mainbar-mobile-list {
    align-items: center;
    background: url('~theme/assets/media/logo-cover.png') 100%;
    display: flex;
    font-size: 18px;
    height: $ui-mainbar-mobile-y;
    justify-content: center;
    list-style-type: none;
    position: fixed;
    width: 100%;
    z-index: 100;

    li {
      width: 100%;
      color: white;
    }
  }

  .mainbar-mobile-list-title {
    text-align: center;
  }

  .mainbar-mobile-list-toggle-sidebar-left {
    color: white;
    padding-top: $ui-space-x / 5;
  }

  .mainbar-mobile-list-toggle-sidebar-right {
    color: white;
    float: right;
  }
</style>
