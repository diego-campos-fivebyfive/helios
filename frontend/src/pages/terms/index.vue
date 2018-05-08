<template lang="pug">
  .wrapper
    Banner(
      title='Aviso',
      message='É necessário aceitar todos os termos de uso da Plataforma SICES',
      type='info',
      icon='info-circle')
    Notification(ref='notification')
    Panel.panel
      div(slot='header')
        h1.title
          | Termos de Uso
      List(
        slot='section',
        :terms='terms',
        :notification='$refs.notification',
        v-on:getTerms='getTerms')
      Paginator(
        slot='footer',
        :pagination='pagination',
        v-on:paginate='getTerms')
</template>

<script>
  import List from './list'

  export default {
    components: {
      List
    },
    data: () => ({
      terms: [],
      pagination: {}
    }),
    methods: {
      getTerms(page = 1) {
        const uri = `/api/v1/terms/?page=${page}`

        this.axios.get(uri).then(response => {
          this.terms = response.data.results
          this.pagination = response.data.page
        })
      }
    },
    mounted() {
      this.getTerms()
    }
  }
</script>

<style lang="scss" scoped>
  /* Terms Style */
</style>
