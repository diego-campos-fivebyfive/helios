<template lang="pug">
  header.bar
    h1.title {{ getPageTitle }}
    h2.info {{ date }}
    a.messages(v-if='$global.user.sices', href='/messenger')
      Icon.messages-icon(name='envelope')
      label.messages-label(v-if='totalOfMessages')
        | {{ totalOfMessages }}
    nav.menu
      Button(
        type='default-common',
        icon='sign-out',
        link='/logout',
        refresh='true',
        label='Sair',
        pos='first')
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

  export default {
    data: () => ({
      date: '',
      totalOfMessages: null
    }),
    sockets: {
      updateTotalOfMessages(data) {
        this.totalOfMessages = this.totalOfMessages + data
      }
    },
    computed: {
      getPageTitle() {
        return this.$router.history.current.name
      }
    },
    mounted() {
      const uri = '/admin/api/v1/orders/messages/unread_count'
      this.axios.get(uri)
        .then(response => {
          this.totalOfMessages = response.data.unreadMessages
        })

      this.date = getDate()
    }
  }
</script>

<style lang="scss" scoped>
  .bar {
    background-color: $ui-white-regular;
    border-bottom: 1px solid $ui-divider-color;
    color: $ui-text-main;
    display: block;
    padding: $ui-space-y $ui-space-x;
    text-align: right;
    width: 100%;

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
    text-align: left;
  }

  .info {
    display: inline-block;
    font-size: 1rem;
    font-weight: 400;
    margin: $ui-space-y/3 $ui-space-x;
    opacity: 0.8;
  }

  .menu {
    display: inline-block;
  }

  .messages {
    margin-right: 0.75rem;

    .messages-icon {
      display: inline-block;
      z-index: 1;
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
      z-index: 2;
    }
  }

  a {
    color: $ui-gray-regular;
  }
</style>
