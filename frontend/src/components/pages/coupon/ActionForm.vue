<template lang="pug">
  Button(
    v-if='action === "edit"',
    slot='buttons',
    icon='save',
    type='primary-strong',
    label='Salvar',
    pos='single',
    v-on:click.native='editCoupon')
  Button(
    v-else,
    slot='buttons',
    icon='save',
    type='primary-strong',
    label='Salvar',
    pos='single',
    v-on:click.native='createCoupon')
</template>

<script>
  export default {
    props: [
      'action',
      'getPayload'
    ],
    methods: {
      editCoupon() {
        const payload = this.getPayload()

        if (!payload) {
          return
        }

        const uri = `api/v1/coupon/${payload.id}`

        const response = this.axios.put(uri, payload)
          .then(() => 'Cupom editado com sucesso')
          .catch(() => 'Não foi possível editar cupom')

        this.$emit('done', response)
      },
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
