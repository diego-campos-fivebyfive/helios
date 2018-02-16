<template lang="pug">
  Page(sidebar='common', mainbar='common')
    ModalForm(ref='modalForm', v-on:reload='getCoupons')
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
            v-on:click.native='showModalForm')
      Content(slot='section', :coupons='coupons', v-on:reload='getCoupons')
</template>

<script>
  import ModalForm from '@/components/coupon/ModalForm'
  import Content from '@/components/coupon/Content'

  export default {
    components: {
      ModalForm,
      Content
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
  .title {
    text-align: center;
  }
</style>
