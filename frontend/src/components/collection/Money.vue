<template lang="pug">
  Money(v-model='price', v-bind='money')
  span {{price}}
</template>

<script>
  import { Money } from 'v-money'

  export default {
    components: {
      Money
    },
    props: [
      'price'
    ],
    data: () => ({
      money: {
        decimal: ',',
        thousands: '.',
        prefix: 'R$ ',
        suffix: ' #',
        precision: 2,
        masked: false
      }
    })
  }
</script>

<style lang="scss">
  /* Money Style */
</style>
