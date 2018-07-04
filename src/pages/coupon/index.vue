<template lang="pug">
  .wrapper
    Form(
      ref='form',
      v-on:updateList='getCoupons')
    Panel.panel
      div(slot='header')
        h2.title
          | Gerenciamento de Cupons
        nav.menu
          Button(
            class='primary-common',
            label='Novo Cupom',
            :action='showCreateForm')
            Icon(name='plus-square')
      List(
        slot='section',
        :coupons='coupons',
        v-on:getCoupons='getCoupons',
        v-on:showUpdateForm='showUpdateForm')
      Paginator(
        slot='footer',
        :pagination='pagination',
        v-on:paginate='getCoupons')
</template>

<script>
  import List from './list'
  import Form from './form'

  export default {
    components: {
      List,
      Form
    },
    data: () => ({
      coupons: [],
      pagination: {}
    }),
    methods: {
      showCreateForm() {
        this.$refs.form.show('create')
      },
      showUpdateForm(coupon) {
        this.$refs.form.show('edit', coupon)
      },
      getCoupons(pageNumber = 1) {
        const uri = `/api/v1/coupon?page=${pageNumber}`

        this.axios.get(uri).then(response => {
          this.coupons = response.data.results
          this.pagination = response.data.page
        })
      }
    },
    mounted() {
      this.getCoupons()
    }
  }
</script>

<style lang="scss" scoped>
  /* Coupon Style */
</style>
