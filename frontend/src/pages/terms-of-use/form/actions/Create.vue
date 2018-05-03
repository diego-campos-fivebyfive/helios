<template lang="pug">
  Button(
    slot='buttons',
    icon='save',
    type='primary-strong',
    label='Salvar',
    pos='single',
    v-on:click.native='createTerm')
</template>

<script>
  import payload from '@/theme/payload'

  export default {
    props: [
      'payload'
    ],
    methods: {
      createTerm() {
        if (!payload.available(this.payload)) {
          return
        }

        const data = payload.parse(this.payload)

        const uri = '/admin/api/v1/terms/'

        const response = this.axios.post(uri, data)
          .then(() => 'Termo cadastrado com sucesso')
          .catch(() => 'Não foi possível cadastrar termo')

        this.$emit('done', response)
      }
    }
  }
</script>

<style lang="scss">
  /* ContentForm Style */
</style>
