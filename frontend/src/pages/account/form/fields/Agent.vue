<template lang="pug">
  Select(
    :label='field.label',
    :disabled='field.disabled.state',
    :options='options',
    :selected='getCurrentAccount',
    v-on:update='updateAccount')
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
        name: 'Não vinculada'
      }
    }),
    props: [
      'field'
    ],
    methods: {
      updateAccount(select) {
        this.$set(this.field, 'value', {
          id: select.value,
          name: select.text
        })
      }
    },
    computed: {
      getCurrentAccount() {
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
