<template lang="pug">
  header.bar
    nav.util
      .sidebar
        Button.sidebar-toggle(
          :action='updateSidebarType')
          Icon(name='list')
      .title
        | {{ pageTitle }}
      .dropdown
        MenuUser
          .content(slot='content')
            .menu
              .menu-account
                img.menu-account-image(src='~theme/assets/media/logo.png')
                .menu-account-details
                  .menu-account-name
                    | {{ user.name }}
                  .menu-account-company
                    | {{ user.company }}
              .menu-achievements
                Level.widgets-level(:label='user.level')
                .menu-points(:class='user.level')
                  Icon(
                    :class='user.level',
                    name='trophy',
                    scale='0.7')
                  |  {{ user.ranking }} P
            Time.time
</template>

<script>
  import MenuUser from 'theme/template/menu-user'
  import Time from '../mainbar/Time'
  import $locale from 'locale'

  export default {
    data: () => ({
      pageTitle: '',
      submitting: false
    }),
    components: {
      MenuUser,
      Time
    },
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
  $accountImageSize: 45px;

  .util {
    align-items: center;
    background: url('~theme/assets/media/logo-cover.png') 100%;
    color: $ui-white-regular;
    display: flex;
    height: $ui-mainbar-mobile-y;
    justify-content: space-between;
    position: fixed;
    width: 100%;
    z-index: 100;

    .sidebar-toggle {
      color: $ui-white-regular;
    }

    .title {
      font-size: 18px;
    }
  }

  .menu-achievements {
    font-size: $ui-font-size-main;
    min-width: 100px;
  }

  .menu {
    color: $ui-gray-dark;
    margin: $ui-space-x / 2;
    display: flex;
    justify-content: space-between;

    .menu-account {
      display: flex;

      .menu-account-details {
        margin: 0 $ui-space-x / 2;
      }

      .menu-account-name {
        font-weight: 400;
        margin-bottom: $ui-space-x / 5;
        font-size: 15px;
      }

      .menu-account-company {
        font-weight: 100;
        font-size: $ui-font-size-main;
      }
    }

    .menu-account-image {
      width: $accountImageSize;
      height: $accountImageSize;
    }
  }

  .menu-points {
    border: 1px solid;
    border-radius: 0 0 3px 3px;
    font-size: $ui-font-size-main;
    padding: $ui-space-x / 5;
    text-align: center;
    vertical-align: middle;
  }

  .time {
    border-top: 1px solid $ui-gray-lighter;
    color: $ui-gray-dark;
    display: flex;
    font-size: 0.85em;
    justify-content: center;
    padding: $ui-space-x / 2;
    text-transform: capitalize;
  }

  .widgets-level {
    font-size: $ui-font-size-main;
  }

  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.6s;
  }

  .fade-enter, .fade-leave-to {
    opacity: 0;
  }

  @each $label, $color in $level-colors {
    .#{$label} {
      color: $color;
      border-color: $color;
    }
  }
</style>
