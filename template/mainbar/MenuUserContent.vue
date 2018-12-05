<template lang="pug">
  .wrapper
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
    Time.datetime
</template>

<script>
  import Time from './Time'

  export default {
    components: {
      Time
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

  .menu {
    color: $ui-gray-dark;
    margin: $ui-space-x / 2;
    display: flex;
    justify-content: space-between;

    .menu-account {
      display: flex;

      .menu-account-details {
        margin: 0 $ui-space-x / 2;
        text-align: left;
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

  .menu-achievements {
    font-size: $ui-font-size-main;
    min-width: 100px;
  }

  .menu-points {
    border: 1px solid;
    border-radius: 0 0 3px 3px;
    font-size: $ui-font-size-main;
    padding: $ui-space-x / 5;
    text-align: center;
    vertical-align: middle;
  }

  .datetime {
    border-top: 1px solid $ui-gray-lighter;
    color: $ui-gray-dark;
    display: flex;
    font-size: 0.85em;
    justify-content: center;
    padding: $ui-space-x / 2;
    text-transform: capitalize;
  }

  @each $level, $background in $levels-background {
    .#{$level} {
      color: $background;
      border-color: $background;
    }
  }
</style>
