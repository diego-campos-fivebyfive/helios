<template lang="pug">
  .bar
    transition(name='fade')
      .backdrop(
        v-show='menuOpen',
        v-on:click='toggleMenu')
    Button.action(
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
      menuOpen: false
    }),
    methods: {
      toggleMenu() {
        this.menuOpen = !this.menuOpen
      },
      logout() {
        this.$router.push({ path: '/logout' })
      },
      userSettings() {
        this.toggleMenu()
        this.$router.push({ path: '/member/profile' })
      }
    }
  }
</script>

<style lang="scss" scoped>
  $menu-user-width: 350px;

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
    border: 1px solid $ui-gray-light;
    position: absolute;
    right: 0;
    top: 0;
    top: $ui-mainbar-mobile-y;
    width: 100%;
  }

  .action {
    color: $ui-white-regular;
    float: right;
  }

  .panel-actions {
    background: $ui-white-regular;
    border-top: 1px solid $ui-gray-light;
    bottom: 0;
    font-size: $ui-font-size-main;
    width: 100%;
    display: flex;
    justify-content: space-between;

    button {
      margin: $ui-space-x / 5;
      position: relative;
    }
  }

  @media screen and (min-width: $ui-size-md) {
    .panel {
      width: $menu-user-width;
    }
  }

  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.6s;
  }

  .fade-enter, .fade-leave-to {
    opacity: 0;
  }

  .slide-fade-enter-active {
    transition: all 0.5s cubic-bezier(0, 1, 0.5, 1);
  }

  .slide-fade-leave-active {
    transition: all 0.2s ease;
  }

  .slide-fade-enter, .slide-fade-leave-to {
    transform: translateY(-45px);
    opacity: 0;
  }
</style>
