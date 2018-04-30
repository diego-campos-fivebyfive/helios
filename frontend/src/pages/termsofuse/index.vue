<template lang="pug">
  Panel.panel
    div(slot='header')
      slot(name='heading')
        h1.title Lista de Termos de Uso
      Paginator(
        slot='footer',
        :pagination='pagination',
        v-on:paginate='getTerms')
</template>

<script>
  export default {
    data: () => ({
      terms: [],
      pagination: {}
    }),
    methods: {
      getTerms(pageNumber = 1) {
        const uri = `/admin/api/v1/terms?page=${pageNumber}`

        this.axios.get(uri).then(response => {
          console.log(response)
          this.coupons = response.data.results
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
