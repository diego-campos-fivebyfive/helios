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
      'payload',
      'disabled'
    ],
    methods: {
      editCoupon() {
        if (this.disabled) {
          return
        }

        const uri = `api/v1/coupon/${this.payload.id}`

        const response = this.axios.put(uri, this.payload)
          .then(() => 'Cupom editado com sucesso')
          .catch(() => 'Não foi possível editar cupom')

        this.$emit('closeModalForm', response)
      },
      createCoupon() {
        if (this.disabled) {
          return
        }

        const uri = 'api/v1/coupon'

        const response = this.axios.post(uri, this.payload)
          .then(() => 'Cupom cadastrado com sucesso')
          .catch(() => 'Não foi possível cadastrar cupom')

        this.$emit('closeModalForm', response)
      }
    }
  }
</script>

<style lang="scss">
  /* ContentForm Style */
</style>
