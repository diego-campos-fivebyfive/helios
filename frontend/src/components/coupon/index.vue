<template lang="pug">
  Page(sidebar='common')
    Panel.panel
      h2.title(slot='header')
        | Gerenciamento de Cupons
      Content(slot='section', :coupons='coupons')
</template>

<script>
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
        },
      ]
    }))
  }
  /* End Mocked Data */

  export default {
    components: {
      Content
    },
    data: () => ({
      coupons: []
    }),
    mounted() {
      Mock.coupon.then(response => {
        this.coupons = response.data
      })
    }
  }
</script>

<style lang="scss" scoped>
  .panel {
    max-width: calc(100% - #{$ui-space-x});
  }
</style>
