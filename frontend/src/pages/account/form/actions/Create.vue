<template lang="pug">
  Button(
    slot='buttons',
    class='primary-strong',
    label='Salvar',
    :action='createAccoutn')
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
      createAccoutn() {
        if (!payload.available(this.payload)) {
          return
        }

        const data = payload.parse(this.payload)

        const uri = 'api/v1/account/'

        const response = this.axios.post(uri, data)
          .then(() => 'Conta cadastrada com sucesso')
          .catch(() => 'Não foi possível cadastrar conta')

        this.$emit('done', response)
      }
    }
  }
</script>

<style lang="scss">
  /* ContentForm Style */
</style>
