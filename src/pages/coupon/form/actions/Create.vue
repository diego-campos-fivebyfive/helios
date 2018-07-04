<template lang="pug">
  Button(
    slot='buttons',
    label='Salvar',
    class='primary-strong',
    :action='createCoupon')
    Icon(name='save')
</template>

<script>
  import payload from '@/theme/payload'

  export default {
     props: {
      payload: {
        type: Array,
        required: true
      }
    },
    methods: {
      createCoupon() {
        if (!payload.available(this.payload)) {
          return
        }

        const data = payload.parse(this.payload)

        const uri = 'api/v1/coupon/'

        const response = this.axios.post(uri, data)
          .then(() => 'Cupom cadastrado com sucesso')
          .catch(() => {
            const errorMessage = 'Não foi possível cadastrar cupom'
            return Promise.reject(new Error(errorMessage))
          })

        this.$emit('done', response)
      }
    }
  }
</script>

<style lang="scss">
  /* ContentForm Style */
</style>
