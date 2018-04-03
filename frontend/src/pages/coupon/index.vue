<template lang="pug">
  .wrapper
    ContentForm(
      ref='contentForm',
      v-on:getCoupons='getCoupons')
    Panel.panel
      div(slot='header')
        h2.title
          | Gerenciamento de Cupons
        nav.menu
          Button(
            type='primary-common',
            icon='plus-square',
            label='Novo Cupom',
            pos='single',
            v-on:click.native='show()')
      Content(
        slot='section',
        :coupons='coupons',
        v-on:getCoupons='getCoupons',
        v-on:show='show')
      Paginator(
        slot='footer',
        :pagination='pagination',
        v-on:paginate='getCoupons')
</template>

<script>
  import Content from './list'
  import ContentForm from './form'

  export default {
    components: {
      Content,
      ContentForm
    },
    data: () => ({
      coupons: [],
      pagination: {}
    }),
    methods: {
      show(coupon = {}) {
        this.$refs.contentForm.show(coupon)
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
