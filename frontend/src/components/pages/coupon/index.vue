<template lang="pug">
  Page(sidebar='common', mainbar='common')
    ModalForm(
      ref='modalForm',
      v-on:getCoupons='getCoupons')
    Panel.panel
      Head(
        slot='header',
        v-on:showModalForm='showModalForm')
      Content(
        slot='section',
        :coupons='coupons',
        v-on:getCoupons='getCoupons',
        v-on:showModalForm='showModalForm')
      Paginator(
        slot='footer',
        :pagination='pagination',
        v-on:paginate='getCoupons')
</template>

<script>
  import Content from './Content'
  import Head from './Head'
  import ModalForm from './ModalForm'

  export default {
    components: {
      Content,
      Head,
      ModalForm
    },
    data: () => ({
      coupons: [],
      pagination: {}
    }),
    methods: {
      showModalForm(action, coupon) {
        this.$refs.modalForm.showActionModal(action, coupon)
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
