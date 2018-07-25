<template lang="pug">
  header.bar
    transition(name='fade')
      .header-cover(
        v-show='getStateTwigModal()',
        v-on:click='toggleTwigModal')
    h1.title
      | {{ pageTitle }}
    span.ranking(
      v-if='showRanking()')
      | {{ user.ranking }} pontos
    Badge.badge(
      v-if='showBadge()',
      :level='user.level')
    span.info {{ date }}
    nav.menu
      router-link.menu-item.messages(
        v-if='showMessages()',
        to='/messenger',
        class='')
        Icon.messages-icon(
          name='envelope')
        label.messages-label(v-if='showTotalMessages()')
          | {{ totalOfMessages }}
      a.menu-item.leave(
        href='/logout')
        Icon(name='sign-out')
        span Sair
</template>

<script>
  const getDate = () => {
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

    return `${dayInTheWeek}, ${day} de ${month} de ${year}`
  }

  import ringNotify from 'theme/assets/media/ring-notify.wav'
  export default {
    props: {
      handleTwigModal: {
        type: Object,
        required: true
      }
    },
    data: () => ({
      date: '',
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
      this.setCurrentDate()
      this.setUser()
    },
    mounted() {
      if (this.user.sices) {
        const uri = '/admin/api/v1/orders/messages/unread_count'
        this.axios.get(uri)
          .then(response => {
            this.totalOfMessages = response.data.unreadMessages
          })
      }
    },
    methods: {
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
        this.date = getDate()
      },
      showRanking() {
        return this.user.ranking
          && this.user.type !== 'child'
          && !this.user.sices
      },
      showBadge() {
        return this.user
          && this.user.level
          && !this.user.sices
      },
      showMessages() {
        return this.user.sices
      },
      showTotalMessages() {
        return this.totalOfMessages
      },
      getStateTwigModal() {
        return this.handleTwigModal.state
      },
      toggleTwigModal() {
        return this.handleTwigModal.toogle
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

  .badge {
    margin: 0 $ui-space-x/3;
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

    * {
      vertical-align: middle;
    }
  }

  .title {
    display: inline-block;
    float: left;
    font-size: 2rem;
    font-weight: 300;
    margin-left: $ui-sidebar-toogle-x + $ui-space-x;
    text-align: left;
  }

  .ranking {
    margin-right: $ui-space-x/2;
  }

  .info {
    display: inline-block;
    font-size: 1rem;
    font-weight: 400;
    margin: $ui-space-y/1.25 $ui-space-x/2;
    opacity: 0.8;
  }

  .menu {
    display: flex;
    float: right;

  .menu-item {
      margin: 10px
    }
  }

  .messages {
    .messages-icon {
      display: inline-block;
      z-index: 105;
    }

    .messages-label {
      background-color: $ui-orange-light;
      border-radius: 0.25rem;
      color: $ui-white-regular;
      font-size: 0.8rem;
      font-weight: 600;
      padding: 0.25rem;
      position: absolute;
      right: 7.75rem;
      top: 0.8rem;
      z-index: 105;
    }
  }

  a {
    color: $ui-gray-regular;
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

    .badge {
      display: none;
    }

    .ranking {
      display: none;
    }
  }
</style>
