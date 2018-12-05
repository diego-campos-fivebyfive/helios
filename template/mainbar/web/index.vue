<template lang="pug">
  header.bar
    h1.title
      | {{ pageTitle }}
    nav.util
      Widgets.widget
      Time.time
      QuickAccess.quick-access
      .menu-access
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
    },
    computed: {
      user() {
        return {
          name: localStorage.getItem('userName'),
          company: localStorage.getItem('userCompany'),
          ranking: localStorage.getItem('userRanking'),
          level: localStorage.getItem('userLevel')
        }
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
    padding: $ui-space-x / 1.5;
    position: relative;
    text-align: right;
    width: 100%;

    @include clearfix;
  }

  .title {
    display: inline-block;
    float: left;
    font-size: 1.8rem;
    font-weight: 300;
    text-align: left;
    margin: -($ui-space-x / 4) $ui-tabbar-mobile-y;
  }

  .util {
    display: flex;
    justify-content: flex-end;
  }

  .widget {
    display: inline-block;
  }

  .quick-access {
    display: flex;
    justify-content: flex-end;
  }

  .leave {
    color: $ui-gray-regular;
  }

  .leave-label {
    vertical-align: super;
  }

  @media screen and (max-width: $ui-size-md) {
    .time {
      display: none;
    }
    .leave-label {
      display: none;
    }
  }
</style>
