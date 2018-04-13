<template lang="pug">
  Button(
    slot='buttons',
    icon='save',
    type='primary-strong',
    label='Salvar',
    pos='single',
    v-on:click.native='createCoupon')
</template>

<script>
  import payload from '@/theme/payload'

  export default {
    props: [
      'payload'
    ],
    methods: {
      createCoupon() {
        if (!payload.available(this.payload)) {
          return
        }

        const data = payload.parse(this.payload)

        const uri = 'api/v1/coupon/'

        const response = this.axios.post(uri, data)
          .then(() => 'Cupom cadastrado com sucesso')
          .catch(() => 'Não foi possível cadastrar cupom')

        this.$emit('done', response)
      }
    }
  }
</script>

<style lang="scss">
  /* ContentForm Style */
</style>
