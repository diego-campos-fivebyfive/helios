<template lang="pug">
  .menu-user(:class='platform')
    transition(name='fade')
      .backdrop(
        v-show='menuOpen',
        v-on:click='toggleMenu')
    Button.action(
      :action='toggleMenu',
      v-if='hideOnMobile()')
      Avatar.avatar(:current='user.name')
    Button.action(
      v-else,
      :action='toggleMenu')
      Icon(name='user')
    transition(name='slide-fade')
      .panel(v-show='menuOpen')
        slot.panel-slot(name='content')
        .panel-actions
          Button.panel-actions-settings(
            class='primary-common size-small ',
            :action='userSettings')
            Icon(name='user', scale='0.8')
            |  {{ $locale.theme.template.myData }}
          Button(
            class='primary-common size-small',
            :action='logout')
            Icon(name='sign-out', scale='0.8')
            |  {{ $locale.theme.template.signOut }}
</template>

<script>
  import $locale from 'locale'

  export default {
    data: () => ({
      menuOpen: false,
      platform: process.env.PLATFORM !== 'web' ? 'mobile' : ''
    }),
    methods: {
      toggleMenu() {
        this.menuOpen = !this.menuOpen
      },
      logout() {
        if (process.env.PLATFORM === 'web') {
          window.location = `${process.env.API_URL}/logout`
        } else {
          this.$router.push({ path: '/logout' })
        }
      },
      userSettings() {
        this.toggleMenu()
        this.$router.push({ path: '/member/profile' })
      },
      hideOnMobile() {
        return process.env.PLATFORM === 'web'
      }
    },
    computed: {
      user() {
        return {
          name: localStorage.getItem('userName')
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  $menu-user-width: 370px;

  .backdrop {
    background: rgba(0, 0, 0, 0.5);
    height: 100vh;
    left: 0;
    position: absolute;
    top: $ui-mainbar-mobile-y;
    width: 100vw;
  }

  .panel {
    background: $ui-white-regular;
    position: absolute;
    right: 0;
    top: $ui-mainbar-mobile-y;
    width: 100%;
    z-index: 150;
  }

  .web {
    .backdrop {
      background: rgba(0, 0, 0, 0.2);
      top: $ui-mainbar-y;
    }

    .panel {
      top: $ui-mainbar-y;
    }
  }

  .action {
    color: $ui-white-regular;
    float: right;
  }

  .panel-actions {
    background: $ui-white-regular;
    border-top: 1px solid $ui-gray-lighter;
    bottom: 0;
    display: flex;
    font-size: $ui-font-size-main;
    justify-content: space-between;
    padding: 0 ($ui-space-x / 5) 0 ($ui-space-x / 5);
    width: 100%;

    button {
      margin: $ui-space-x / 5;
      position: relative;
    }
  }

  .web {
    .action {
      color: $ui-gray-regular;
      padding: 0;
      margin: 0 ($ui-space-x / 2);

      svg {
        margin-right: $ui-space-x / 5;
      }
    }
  }

  @media screen and (min-width: $ui-size-md) {
    .panel {
      width: $menu-user-width;
    }
  }

  .avatar {
    background: $ui-blue-light;
    color: $ui-white-regular;
    height: 2.5rem;
    width: 2.5rem;
  }

  .action:hover {
    opacity: 0.5;
    transition: 1s;
  }

  .fade-enter-active {
    transition: opacity 0.6s;
  }

  .fade-enter, .fade-leave-to {
    opacity: 0;
  }

  .slide-fade-enter-active {
    transition: all 0.5s cubic-bezier(0, 1, 0.5, 1);
  }

  .slide-fade-enter, .slide-fade-leave-to {
    transform: translateY(-45px);
    opacity: 0;
  }
</style>
