<template lang="pug">
  Panel.panel
    div(slot='header')
      h1.title
        | Gerenciamento de Termos de Uso
      nav.menu
        Button(
          type='primary-common',
          icon='plus-square',
          label='Novo Termo',
          pos='single')
    List(
      slot='section',
      :terms='terms',
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
      getTerms(pageNumber = 1) {
        const uri = `/admin/api/v1/terms?page=${pageNumber}`

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
/* Terms Of Use Style */
</style>
