<template lang="pug">
  header.mobile-mainbar
    ul
      li.mobile-mainbar-title
        | {{ pageTitle }}
      label.mobile-mainbar-toggle-sidebar
        Button.mobile-mainbar-toggle-sidebar-left(
          :action='updateSidebarType')
          Icon(name='list')
        Button.mobile-mainbar-toggle-sidebar-right(
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
  .mobile-mainbar ul {
    position: relative;
    background: url(/static/logo-cover.03a9c85.png) 100%;
    color: white;
    padding: 0;
    margin: 0;
    cursor: auto;
    font-size: 18px;
    list-style-type: none;
    box-shadow: 0 5px 5px -5px #333;
    z-index: 100;

    &:after {
      content: "";
      display: table;
      clear: both;
    }

    button, a {
      color: white;

      path {
        fill: #fff;
      }

      &.icon-close {
        display: none;
        padding: 15px;
      }
    }

    .mobile-mainbar-toggle-sidebar-left {
      height: 40px;
      width: 65px;
    }

    .mobile-mainbar-toggle-sidebar-right {
      position: absolute;
      margin: 6px 71vw;
    }

    li {
      width: 100%;
      height: $ui-mainbar-mobile-y;
      line-height: $ui-mainbar-mobile-y;
      text-align: center;
      float: left;

      a {
        display: block;
        color: #333;
        width: 100%;
        height: 100%;
        text-decoration: none;
      }
    }
  }

  .mobile-mainbar-toggle-checkbox {
    display: none;
    &.active ~ .menu-button,
    &:checked ~ .menu-button {

      .icon-close {
        display: block;
      }

      .icon-open {
        display: none;
      }

      &:after {
        opacity: 1;
        pointer-events: auto;
        transition: opacity 0.3s cubic-bezier(0,0,0.3,1);
      }
    }
  }

  .mobile-mainbar-toggle-sidebar {
    position: absolute;
    top: 0;
    left: 0;

    &:after {
      opacity: 0;
      top: 45px;
      content: "";
      width: 50vw;
      display: block;
      position: fixed;
      height: 50vh;
      background: rgba(0,0,0,0.5);
      content: "";
      pointer-events: none;
      transition: opacity 0.2s cubic-bezier(0,0,0.3,1);
      transition-delay: 0.1s;
    }
  }
</style>
