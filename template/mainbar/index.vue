<template lang="pug">
  header.bar
    transition(name='fade')
      .header-cover(
        v-show='getStateTwigModal()',
        v-on:click='toggleTwigModal')
    h1.title
      | {{ pageTitle }}
    nav.util
      Widgets.widget
      Time.time
      Menu.menu
      a.leave(href='/logout')
        Icon(name='sign-out')
        span.leave-label
          | Sair
</template>

<script>
  import ringNotify from 'theme/assets/media/ring-notify.wav'

  import Menu from '@/app/theme/Menu'
  import Widgets from '@/app/theme/Widgets'

  import Time from './Time'

  export default {
    components: {
      Time,
      Menu,
      Widgets
    },
    props: {
      handleTwigModal: {
        type: Object,
        required: true
      }
    },
    data: () => ({
      pageTitle: ''
    }),
    watch: {
      $route: {
        handler: 'setPageTitle',
        immediate: true
      }
    },
    methods: {
      getStateTwigModal() {
        return this.handleTwigModal.state
      },
      setPageTitle() {
        this.pageTitle = this.$router.history.current.meta.title
      },
      toggleTwigModal() {
        return this.handleTwigModal.toogle
      }
    }
  }
</script>

<style lang="scss" scoped>
  $head-border-size: 1px;

  .fade-enter-active,
  .fade-leave-active {
    transition: all 150ms ease;
  }

  .fade-enter,
  .fade-leave-to {
    opacity: 0;
  }

  .header-cover {
    background-color: rgba(0, 0, 0, 0.5);
    height: calc(100% + #{$head-border-size});
    left: 0;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 250;
  }

  .bar {
    background-color: $ui-white-regular;
    border-bottom: $head-border-size solid $ui-divider-color;
    color: $ui-text-main;
    height: $ui-mainbar-y;
    max-height: $ui-mainbar-y - $head-border-size;
    padding: $ui-space-y $ui-space-x;
    position: relative;
    text-align: right;
    width: 100%;
    z-index: 100;

    @include clearfix;
  }

  .title {
    display: inline-block;
    float: left;
    font-size: 2rem;
    font-weight: 300;
    margin-left: $ui-sidebar-toogle-x + $ui-space-x;
    text-align: left;
  }

  .util {
    display: flex;
    float: right;
  }

  .widget {
    display: inline-block;
  }

  .menu {
    display: flex;
    float: right;
  }

  .leave {
    color: $ui-gray-regular;
    margin: 10px;
  }

  .leave-label {
    vertical-align: super;
  }

  .time {
    margin: $ui-space-y/1.25 $ui-space-x/2;
  }

  @media (max-width: $ui-size-lg) {
    .time {
      display: none;
    }
  }
</style>
