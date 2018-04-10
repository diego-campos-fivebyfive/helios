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
      }
    },
    mounted() {
      document.addEventListener('keyup', event => {
        const enterCode = 13

        if (event.keyCode !== enterCode) {
          return
        }

        if (this.action === 'edit') {
          this.editCoupon()
          return
        }

        this.createCoupon()
      })
    }
  }
</script>

<style lang="scss">
  /* ContentForm Style */
</style>
