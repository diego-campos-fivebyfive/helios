<template lang="pug">
  Button(
    slot='buttons',
    class='primary-strong',
    label='Salvar',
    :action='editCoupon')
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
      editCoupon() {
        if (!payload.available(this.payload)) {
          return
        }

        const data = payload.parse(this.payload)

        const uri = `/admin/api/v1/terms/${data.id}`

        const response = this.axios.put(uri, data)
          .then(() => 'Termo editado com sucesso')
          .catch(() => 'Não foi possível editar termo')

        this.$emit('done', response)
      }
    }
  }
</script>

<style lang="scss">
  /* ContentForm Style */
</style>
