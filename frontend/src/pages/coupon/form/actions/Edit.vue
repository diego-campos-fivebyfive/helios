<template lang="pug">
  Button(
    slot='buttons',
    label='Salvar',
    class='primary-strong',
    :action='editCoupon')
    Icon(name='save')
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
          .catch(() => {
            const errorMessage = 'Não foi possível editar cupom'
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
