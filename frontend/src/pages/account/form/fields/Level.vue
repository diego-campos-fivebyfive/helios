<template lang="pug">
  Select(
    :label='field.label',
    :options='options',
    :selected='getCurrentLevel',
    v-on:update='updateLevel')
</template>

<script>
  import Select from '@/theme/collection/Select'

  export default {
    components: {
      Select
    },
    data: () => ({
      options: [],
      defaultOption: {
        id: '',
        name: 'Não vinculado'
      }
    }),
    props: [
      'field'
    ],
    methods: {
      updateLevel(select) {
        this.$set(this.field, 'value', {
          id: select.value,
          name: select.text
        })
      }
    },
    computed: {
      getCurrentLevel() {
        return (
          this.field.value
          && this.field.value.id
        )
          ? this.field.value.id
          : this.defaultOption.id
      }
    },
    mounted() {
      this.axios.get('api/v1/account/available')
        .then(response => {
          const accounts = response.data
          accounts.unshift(this.defaultOption)

          this.options = accounts.map(account => ({
            value: account.id,
            text: account.name
          }))
        })
    }
  }
</script>

<style lang="scss">
  /* ContentForm Style */
</style>
