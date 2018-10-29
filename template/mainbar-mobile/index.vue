<template lang="pug">
  .wrapper-mainbar
    header.mainbar-mobile
      ul.mainbar-mobile-list
        li.mainbar-mobile-list-button-left
          Button.mainbar-mobile-list-toggle-sidebar-left(
            :action='updateSidebarType')
            Icon(name='list')
        li.mainbar-mobile-list-title
          | {{ pageTitle }}
        li.mainbar-mobile-list-button-right
          Button.mainbar-mobile-list-toggle-sidebar-right(
            v-if='!submitting',
            :action='logout')
            Icon(name='sign-out')
          Icon.mainbar-mobile-list-logout(
            class='rotate', name='repeat', v-else)
</template>

<script>
  export default {
    data: () => ({
      pageTitle: '',
      submitting: false
    }),
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
      logout() {
        this.submitting = true
        this.axios.post(`${process.env.API_URL}/logout`)
          .then(() => {
            this.submitting = false
            localStorage.clear()
            document.cookie = ''

            this.$router.push({ path: '/login' })
          })
      },
      setPageTitle() {
        this.pageTitle = this.$router.history.current.meta.title
      }
    }
  }
</script>

<style lang="scss" scoped>
  .mainbar-mobile-list {
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

    .mainbar-mobile-list-button-left {
      width: 20%;
    }

    .mainbar-mobile-list-title {
      width: 60%;
    }

    .mainbar-mobile-list-button-right {
      width: 20%;
    }
  }

  .mainbar-mobile-list-title {
    text-align: center;
  }

  .mainbar-mobile-list-toggle-sidebar-left {
    color: white;
    padding-top: $ui-space-x / 5;
  }

  .mainbar-mobile-list-toggle-sidebar-right {
    color: white;
    float: right;
  }

  .mainbar-mobile-list-logout {
    float: right;
    margin: $ui-space-y;
  }

  .rotate {
    animation: rotate 1s infinite;
  }

  @keyframes rotate {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(315deg);
    }
  }
</style>
