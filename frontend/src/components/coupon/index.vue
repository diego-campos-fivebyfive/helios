<template lang="pug">
  Page(sidebar='common', mainbar='common')
    Modal(:open='modal.open', v-on:close='modal.open = false')
      h1.title(slot='header')
        | Novo Cupom
      Action(slot='section', ref='action')
      Button(
        slot='buttons',
        icon='save',
        type='primary-strong',
        label='Salvar',
        pos='single',
        v-on:click.native='send')
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
            v-on:click.native='showActionModal')
      Content(slot='section', :coupons='coupons', v-on:reload='getCoupons')
</template>

<script>
  import Action from '@/components/coupon/Action'
  import Content from '@/components/coupon/Content'

  export default {
    components: {
      Action,
      Content
    },
    data: () => ({
      coupons: [],
      modal: {
        open: false
      }
    }),
    mounted() {
      this.getCoupons()
    },
    methods: {
      showActionModal() {
        this.modal.open = true
      },
      send() {
        this.$refs.action.send()
      },
      getCoupons() {
        this.axios.get('/api/v1/coupon/').then(response => {
          this.coupons = response.data.results
        })
      }
    }
  }
</script>

<style lang="scss" scoped>
  .title {
    text-align: center;
  }
</style>
