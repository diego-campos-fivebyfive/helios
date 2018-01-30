<template lang="pug">
  Page(sidebar='common')
    Panel
      div(slot='header')
        h1.title {{ page.title }}
        h2.info
          | Contas encontradas: {{ account.info.total }}
        nav.menu
          Button(
            type='primary-common',
            icon='plus-square',
            link='#linkB',
            label='Nova Conta',
            pos='first')
          Button(type='primary-strong', icon='random', pos='last')
</template>

<script>
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
