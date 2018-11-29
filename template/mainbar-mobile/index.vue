<template lang="pug">
  header.mainbar-mobile
    ul
      li.mainbar-mobile-sidebar
        Button.mainbar-mobile-toggle-sidebar(
          :action='updateSidebarType')
          Icon(name='list')
      li.mainbar-mobile-title
        | {{ pageTitle }}
      li.mainbar-mobile-options
        MenuUser
          .mainbar-mobile-options-content(slot='content')
            .mainbar-mobile-options-content-logo
              img(src='~theme/assets/media/logo.png')
            .mainbar-mobile-options-name
              | {{ user.name }}
            .mainbar-mobile-options-company
              | {{ user.company }}
            .mainbar-mobile-options-details
              .mainbar-mobile-options-details-first
                .mainbar-mobile-options-level
                  span.dot(:class='user.level')
                    | &#9679;
                  |  {{ user.level }}
                .mainbar-mobile-options-ranking
                  | {{ user.ranking }} pontos
              .time
                Time
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
  @each $label, $color in $level-colors {
    .#{$label} {
      color: $color;
    }
  }

  .dot {
    font-size: 20px;
  }

  .mainbar-mobile ul {
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
      color: white;
      width: 100%;
    }

    .mainbar-mobile-sidebar {
      width: 20%;
    }

    .mainbar-mobile-title {
      width: 60%;
    }

    .mainbar-mobile-options {
      width: 20%;
    }
  }

  .mainbar-mobile-title {
    text-align: center;
  }

  .mainbar-mobile-toggle-sidebar {
    color: white;
    padding-top: $ui-space-x / 5;
  }

  .mainbar-mobile-toggle-sidebar-right {
    color: white;
    float: right;
  }

  .mainbar-mobile-logout {
    float: right;
    margin: $ui-space-y;
  }

  .mainbar-mobile-options-content {
    //line-height: 22px;
    margin: 15px;
    font-size: 14px;

    img {
      width: 60px;
    }
  }

  .mainbar-mobile-options-content {
    color: $ui-gray-dark;
  }

  .mainbar-mobile-options-name {
    text-align: center;
    font-size: 1.20em;
  }

  .mainbar-mobile-options-company {
    text-align: center;
    font-weight: 100;
    font-size: 1em;
  }

  .mainbar-mobile-options-ranking {
    //font-weight: 100;
    //font-size: 1em;
    padding: 5px;
  }

  .mainbar-mobile-options-content-logo {
    text-align: center;
  }


  .mainbar-mobile-options-details {
    margin-top: 20px;
    border-top: 1px solid $ui-gray-lighter;
    //text-align: center;
    div {
      margin: 5px;
      //display: inline-block;
    }
  }

  .mainbar-mobile-options-details-first {
    display: flex;
    justify-content: center;
  }

  .time {
    display: flex;
    justify-content: center;
    text-transform: capitalize;
    font-size: 0.85em;
  }

  .mainbar-mobile-options-level {
    font-size: 14px;
    width: 110px;
    text-transform: capitalize;
  }
</style>
