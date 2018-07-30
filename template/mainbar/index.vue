<template lang="pug">
  header.bar
    transition(name='fade')
      .header-cover(
        v-show='getStateTwigModal()',
        v-on:click='toggleTwigModal')
    h1.title
      | {{ pageTitle }}
    nav.util
      Widgets.widget(:user='user')
      span.info {{ currentDate }}
      Menu.menu(:user='user')
      a.leave(href='/logout')
        Icon(name='sign-out')
        span.leave-label
          | Sair
</template>

<script>
  import ringNotify from 'theme/assets/media/ring-notify.wav'

  import Menu from '@/app/theme/Menu'
  import Widgets from '@/app/theme/Widgets'

  export default {
    components: {
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
      currentDate: '',
      pageTitle: '',
      user: {},
      totalOfMessages: null
    }),
    watch: {
      $route: {
        handler: 'setPageTitle',
        immediate: true
      }
    },
    created() {
      this.setUser()
    },
    mounted() {
      this.setCurrentDate()
    },
    methods: {
      getStateTwigModal() {
        return this.handleTwigModal.state
      },
      setPageTitle() {
        this.pageTitle = this.$router.history.current.meta.title
      },
      setUser() {
        window.$global.getUser
          .then(user => {
            this.user = user
          })
      },
      setCurrentDate() {
        this.currentDate = getDate()
      },
      toggleTwigModal() {
        return this.handleTwigModal.toogle
      },
      setCurrentDate() {
        const months = [
          'janeiro',
          'fevereiro',
          'março',
          'abril',
          'maio',
          'junho',
          'julho',
          'agosto',
          'setembro',
          'outubro',
          'novembro',
          'dezembro'
        ]

        const daysInTheWeek = [
          'domingo',
          'segunda-feira',
          'terça-feira',
          'quarta-feira',
          'quinta-feira',
          'sexta-feira',
          'sábado'
        ]

        const date = new Date()
        const year = date.getFullYear()
        const month = months[date.getMonth()]
        const day = date.getDate()
        const dayInTheWeek = daysInTheWeek[date.getDay()]

        this.currentDate = `${dayInTheWeek}, ${day} de ${month} de ${year}`
      }
    },
    sockets: {
      updateTotalOfMessages(data) {
        this.totalOfMessages = this.totalOfMessages + data

        const ringMessage = new Audio(ringNotify)
        ringMessage.play()
      }
    },
  }
</script>

<style lang="scss" scoped>
  $head-border-size: 1px;

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

  .info {
    display: inline-block;
    font-size: 1rem;
    font-weight: 400;
    margin: $ui-space-y/1.25 $ui-space-x/2;
    opacity: 0.8;
  }

  .fade-enter-active,
  .fade-leave-active {
    transition: all 150ms ease;
  }

  .fade-enter,
  .fade-leave-to {
    opacity: 0;
  }

  @media (max-width: $ui-size-lg) {
    .info {
      display: none;
    }
  }
</style>
