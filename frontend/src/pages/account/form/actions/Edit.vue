<template lang="pug">
  Button(
    slot='buttons',
    icon='save',
    type='primary-strong',
    label='Salvar',
    pos='single',
    v-on:click.native='editCoupon')
</template>

<script>
  import payload from '@/theme/payload'

  export default {
    props: [
      'payload'
    ],
    methods: {
      editCoupon() {
        if (!payload.available(this.payload)) {
          return
        }

        const data = payload.parse(this.payload)

        const uri = `api/v1/coupon/${data.id}`

        const response = this.axios.put(uri, data)
          .then(() => 'Cupom editado com sucesso')
          .catch(() => 'Não foi possível editar cupom')

        this.$emit('done', response)
      }
    }
  }
</script>

<style lang="scss">
  /* ContentForm Style */
</style>
