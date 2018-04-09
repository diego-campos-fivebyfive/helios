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
  export default {
    methods: {
      createCoupon() {
        const payload = this.getPayload()

        if (!payload) {
          return
        }

        const uri = 'api/v1/coupon/'

        const response = this.axios.post(uri, payload)
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
