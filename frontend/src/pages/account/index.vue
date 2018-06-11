<template lang="pug">
  Panel
    div(slot='header')
      h1.title {{ page.title }}
      h2.info
        | Contas encontradas: {{ account.info.total }}
      nav.menu
        Button(
          class='primary-common',
          label='Nova Conta',
          pos='first',
          link='#')
          Icon(name='plus-square')
        Button(
          class='primary-strong',
          pos='last')
          Icon(name='random')
      Content(
        slot='section',
        :accounts='account')
</template>

<script>
  import Content from './list'
  /* Mocked Data */
  const page = {
    title: 'Contas'
  }

  const account = new Promise(resolve => resolve({
    info: {
      total: 753
    }
  }))
  /* End Mocked Data */

  export default {
    components: {
      Content
    },
    data: () => ({
      page: '',
      account: {
        info: {}
      }
    }),
    mounted() {
      this.page = page

      account.then(data => {
        this.account = data
      })
    }
  }
</script>

<style lang="scss" scoped>
  /* Account Style */
</style>
