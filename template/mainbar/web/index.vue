<template lang="pug">
  header.bar
    h1.title
      | {{ pageTitle }}
    .util
      .widget
        Widgets
      .time
        Time
      .quick-access
        QuickAccess
      .menu-user
        MenuUser
          MenuUserContent(slot='content')
</template>

<script>
  import QuickAccess from '../QuickAccess'
  import Widgets from '@/../theme/Widgets'
  import Time from '../Time'
  import MenuUserContent from '../MenuUserContent'
  import MenuUser from 'theme/template/menu-user'
  import $locale from 'locale'

  export default {
    components: {
      QuickAccess,
      Time,
      Widgets,
      MenuUser,
      MenuUserContent
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
  $account-image-size: 45px;

  .bar {
    background-color: $ui-white-regular;
    border-bottom: 1px solid $ui-gray-lighter;
    color: $ui-text-main;
    height: $ui-mainbar-y;
    max-height: $ui-mainbar-y - $head-border-size;
    width: 100%;

    @include clearfix;
  }

  .title {
    display: inline-block;
    font-size: 1.8rem;
    font-weight: 300;
    line-height: 2;
    margin: 0 ($ui-space-x * 2.5);
  }

  .util {
    align-items: center;
    display: flex;
    float: right;
    height: 100%;
    justify-content: flex-end;
    padding: 0 $ui-space-x / 1.8;
  }

  .leave {
    color: $ui-gray-regular;
  }

  .leave-label {
    vertical-align: super;
  }

  .time {
    color: $ui-gray-regular;
  }

  @media screen and (max-width: $ui-size-md) {
    .time {
      display: none;
    }

    .leave-label {
      display: none;
    }

    .quick-access {
      display: none;
    }
  }
</style>
