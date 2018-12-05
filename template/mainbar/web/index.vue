<template lang="pug">
  header.bar
    h1.title
      | {{ pageTitle }}
    ul.util
      li.widget
        Widgets
      li.time
        Time
      li.quick-access
        QuickAccess
      li.menu-user
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
    color: $ui-text-main;
    height: $ui-mainbar-y;
    max-height: $ui-mainbar-y - $head-border-size;
    width: 100%;

    @include clearfix;
  }

  .title {
    display: inline-block;
    line-height: 2;
    font-size: 1.8rem;
    font-weight: 300;
    margin: 0 ($ui-space-x * 2.5);
  }

  .util {
    float: right;
    list-style-type: none;
    display: flex;
    padding: 0 $ui-space-x;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    height: 100%;
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
