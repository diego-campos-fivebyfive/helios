<template lang="pug">
  .wrapper
    Notification(ref='notification')
    Form(
      ref='form',
      v-on:updateList='getTerms')
    Panel.panel
      div(slot='header')
        h1.title
          | Gerenciamento de Termos de Uso
        nav.menu
          Button(
            class='primary-common',
            label='Novo Termo',
            :action='() => show("create")')
            Icon(name='plus-square')
      List(
        slot='section',
        :terms='terms',
        :notification='$refs.notification',
        v-on:getTerms='getTerms',
        v-on:show='show')
      Paginator(
        slot='footer',
        :pagination='pagination',
        v-on:paginate='getTerms')
</template>

<script>
  import Form from './form'
  import List from './list'

  export default {
    components: {
      Form,
      List
    },
    data: () => ({
      terms: [],
      pagination: {}
    }),
    methods: {
      show(action, term = {}) {
        this.$refs.form.show(action, term)
      },
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
