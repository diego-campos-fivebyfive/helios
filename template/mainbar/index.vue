<template lang="pug">
  header.bar
    h1.title
      | {{ pageTitle }}
    nav.util
      Widgets.widget
      Time.time
      Menu.menu
      a.leave(href='/logout')
        Icon(name='sign-out')
        span.leave-label
          | {{ $locale.template.signOut }}
</template>

<script>
  import $locale from 'locale'
  import Menu from '@/app/theme/Menu'
  import Widgets from '@/app/theme/Widgets'

  import Time from './Time'

  export default {
    components: {
      Time,
      Menu,
      Widgets
    },
    data: () => ({
      pageTitle: ''
    }),
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
  $head-border-size: 1px;

  .bar {
    background-color: $ui-white-regular;
    border-bottom: $head-border-size solid $ui-divider-color;
    color: $ui-text-main;
    height: $ui-mainbar-y;
    max-height: $ui-mainbar-y - $head-border-size;
    padding: $ui-space-y $ui-space-x;
    position: relative;
    text-align: right;
    width: 100%;
    z-index: 100;

    @include clearfix;
  }

  .title {
    display: inline-block;
    float: left;
    font-size: 2rem;
    font-weight: 300;
    margin-left: $ui-sidebar-toogle-x + $ui-space-x;
    text-align: left;
  }

  .util {
    display: flex;
    float: right;
  }

  .widget {
    display: inline-block;
  }

  .menu {
    display: flex;
    float: right;
  }

  .leave {
    color: $ui-gray-regular;
    margin: 10px;
  }

  .leave-label {
    vertical-align: super;
  }

  .time {
    margin: $ui-space-y/1.25 $ui-space-x/2;
  }

  @media (max-width: $ui-size-lg) {
    .time {
      display: none;
    }
  }
</style>
