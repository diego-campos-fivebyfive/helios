<template lang="pug">
  Page(sidebar='common', mainbar='common')
    Modal(:open='modal.open', v-on:close='modal.open = false')
      Action(slot='section')
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
      Content(slot='section', :coupons='coupons')
</template>

<script>
  import Action from '@/components/coupon/Action'
  import Content from '@/components/coupon/Content'

  /* Mocked Data */
  const Mock = {
    coupon: new Promise(resolve => resolve({
      data: [
        {
          name: 'Jonny Walker',
          amount: '175,00',
          account: 'Luiz Antunes',
          status: 'não aplicado'
        },
        {
          name: 'Jonny Walker Red Label',
          amount: '1.400,00',
          account: 'Luiz Antunes',
          status: 'aplicado'
        },
        {
          name: 'Jonny Walker Blue Label',
          amount: '1.450,00',
          account: 'Luiz Antunes',
          status: 'aplicado'
        },
        {
          name: 'Jack Daniels Frank Sinatra',
          amount: '799,00',
          account: 'Luiz Antunes',
          status: 'aplicado'
        }
      ]
    }))
  }
  /* End Mocked Data */

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
      Mock.coupon.then(response => {
        this.coupons = response.data
      })
    },
    methods: {
      showActionModal() {
        this.modal.open = true
      }
    }
  }
</script>

<style lang="scss" scoped>
  /* Coupon Style */
</style>
