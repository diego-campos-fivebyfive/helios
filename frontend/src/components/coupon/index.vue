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
        v-on:getCoupons='getCoupons')
</template>

<script>
  import Content from '@/components/coupon/Content'
  import Head from '@/components/coupon/Head'
  import ModalForm from '@/components/coupon/ModalForm'

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
      showModalForm() {
        this.$refs.modalForm.showActionModal()
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
