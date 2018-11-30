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
          .menu(slot='content')
            .menu-main
              .menu-main-image
                img(src='~theme/assets/media/logo.png')
              .menu-main-details
                .menu-main-name
                  | {{ user.name }}
                .menu-main-company
                  | {{ user.company }}
              .menu-details
                .menu-details-achievements
                  .menu-details-achievements-level
                    Level.widgets-level(
                      :label='user.level')

            .time-scope-de
              .menu-details-achievements-ranking
                | {{ user.ranking }}
                span.menu-details-achievements-ranking-label
                  |  pontos
            .time-scope
              Time.time
</template>

<script>
  import MenuUser from 'theme/template/menu-user'
  import Time from '../mainbar/Time'

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
  .util {
    align-items: center;
    background: url('~theme/assets/media/logo-cover.png') 100%;
    display: flex;
    height: $ui-mainbar-mobile-y;
    justify-content: space-between;
    position: fixed;
    width: 100%;
    z-index: 100;

    .sidebar .sidebar-toggle {
      color: white;
    }

    .title {
      color: white;
      font-size: 18px;
    }

    .dropdown {
      color: white;
    }
  }

  .menu {
    color: $ui-gray-dark;
    margin: 15px;
    font-size: 14px;

    .menu-main {
      display: flex;
      justify-content: space-between;
      //flex-direction: column;

      .menu-main-name {
        font-weight: 400;
        margin-bottom: 5px;

      }

      .menu-main-company {
        font-weight: 100;
        font-size: 13px;
      }
    }

    .menu-main-image img {
      width: 45px;
      margin-right: 10px;
    }
  }

  .menu-details-achievements-ranking {
    //text-align: center;
    //padding: 10px;
    font-size: 16px;
  }

  .menu-details-achievements-ranking-label {
    font-size: 11px;
  }

  .menu-details {
    //margin-top: 20px;

  }

  .menu-details-achievements {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    //padding: 10px;
  }

.time-scope-de {
  //border-top: 1px solid $ui-gray-lighter;
  margin-top: 15px;
  text-align: center;
}

.time-scope {
    display: flex;
    justify-content: center;

  text-transform: capitalize;
  border-top: 1px solid $ui-gray-lighter;
  margin-top: 15px;
}
  .time {
    font-size: 0.85em;
    margin-top: 15px;
  }

  .menu-details-achievements-level {
    //font-size: 14px;
    //width: 110px;
    text-transform: capitalize;
  }

  @each $label, $color in $level-colors {
    .#{$label} {
      color: $color;
    }
  }

  .dot {
    font-size: 17px;
  }
</style>
