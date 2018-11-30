<template lang="pug">
  .bar
    transition(name='fade')
      .backdrop(
        v-show='menuOpen',
        v-on:click='toggleMenu')
    Button.action(
      :action='toggleMenu')
      Icon(name='angle-down')
    transition(name='slide-fade')
      .panel(v-show='menuOpen')
        .panel-content
          .panel-slot
            slot(name='content')
          .panel-actions
            Button.panel-actions-settings(
              class='primary-common',
              :action='userSettings')
              Icon(name='male')
              |  Meus dados
            Button.panel-actions-logout(
              class='primary-common',
              :action='logout')
              Icon(name='sign-out')
              |  Sair
</template>

<script>
  export default {
    data: () => ({
      menuOpen: false
    }),
    methods: {
      toggleMenu() {
        this.menuOpen = !this.menuOpen
      },
      logout() {
        this.submitting = true
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
  .backdrop {
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    position: fixed;
    top: 45px;//externo
    left: 0;
  }

  .panel {
    background: #fff;
    border: 1px solid #d8d8d8;
    //height: 250px;
    position: absolute;
    right: 0;
    top: 0;
    top: 45px;//externo
    width: 100%;
  }

  .action {
    color: white;
    float: right;
  }

  .panel-actions {
    //text-align: center;
    bottom: 0;
    //position: absolute;
    width: 100%;
    font-size: 14px;
    //padding: 10px;
    background: white;
    border-top: 1px solid #d6d6d6;

    button {
      margin: 5px;
      position: relative;
    }
  }

  .panel-actions-logout {
    float: right;
  }

  @media screen and (min-width: $ui-size-md) {
    .panel {
      width: 300px;
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
