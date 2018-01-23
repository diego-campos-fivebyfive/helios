<template lang="pug">
  Panel
    div(slot='header')
      h1.title {{ page.title }}
      h2.info
        | {{ account.info.total }} Contas encontradas
      nav.menu
        button button
        button btn
        //Button(:text='Nova Conta')
        //  Icon(name='beer')
        //Button
        //  Icon(name='beer')
</template>

<script>
  import Panel from '@/components/collection/Panel'

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
      Panel
    },
    data: () => ({
      page: '',
      account: {}
    }),
    mounted() {
      this.page = page

      account.then(data => {
        this.account = data
      })
    }
  }
</script>
