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
      coupons: []
    }),
    methods: {
      showModalForm(action, coupon) {
        this.$refs.modalForm.showActionModal(action, coupon)
      },
      getCoupons() {
        this.axios.get('/api/v1/coupon/').then(response => {
          this.coupons = response.data.results
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
