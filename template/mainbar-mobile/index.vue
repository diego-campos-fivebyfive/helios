<template lang="pug">
  .wrapper
    Cover.cover(
      :height='45',
      :speed='8000',
      :scale='10')
    header.bar
      nav.util
        .sidebar
          Button.sidebar-toggle(
            :action='updateSidebarType')
            Icon(name='list')
        h2.title
          | {{ pageTitle }}
        .dropdown
          MenuUser
            .content(slot='content')
              .menu
                .menu-account
                  img(src='~theme/assets/media/logo.png')
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
      pageTitle: ''
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
  $account-image-size: 45px;

  .util {
    align-items: center;
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
      font-size: 1.5rem;
      font-weight: 400;
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

    img {
      width: $account-image-size;
      height: $account-image-size;
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
    font-size: 2em;
  }

  @each $level, $background in $levels-background {
    .#{$level} {
      color: $background;
      border-color: $background;
    }
  }

  .cover {
    background: $ui-orange-dark;
    position: absolute;
    z-index: 100;
  }
</style>
