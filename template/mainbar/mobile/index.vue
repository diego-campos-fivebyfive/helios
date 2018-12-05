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
            MenuUserContent(slot='content')
</template>

<script>
  import MenuUser from 'theme/template/menu-user'
  import MenuUserContent from '../MenuUserContent'
  import $locale from 'locale'

  export default {
    data: () => ({
      pageTitle: ''
    }),
    components: {
      MenuUser,
      MenuUserContent
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
    }
  }
</script>

<style lang="scss" scoped>
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

  .widgets-level {
    font-size: 2em;
  }

  .cover {
    background: $ui-orange-dark;
    position: absolute;
    z-index: 100;
  }
</style>
