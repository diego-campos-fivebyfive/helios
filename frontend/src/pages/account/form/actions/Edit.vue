<template lang="pug">
  Button(
    slot='buttons',
    icon='save',
    type='primary-strong',
    label='Salvar',
    pos='single',
    v-on:click.native='editAccount')
</template>

<script>
  import payload from '@/theme/payload'

  export default {
    props: [
      'payload'
    ],
    methods: {
      editAccount() {
        if (!payload.available(this.payload)) {
          return
        }

        const data = payload.parse(this.payload)

        const uri = `api/v1/account/${data.id}`

        const response = this.axios.put(uri, data)
          .then(() => 'Conta editado com sucesso')
          .catch(() => 'Não foi possível editar conta')

        this.$emit('done', response)
      }
    }
  }
</script>

<style lang="scss">
  /* ContentForm Style */
</style>
