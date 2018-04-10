<template lang="pug">
  Select(
    :label='label',
    :selected='getAccountSelected',
    :options='options',
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
      'currentAccount',
      'label',
      'params',
      'updateField'
    ],
    methods: {
      updateAccount(account) {
        this.updateField({
          name: this.params.name,
          key: 'value',
          value: {
            id: account.value,
            name: account.text
          }
        })
      }
    },
    computed: {
      getAccountSelected() {
        return (
          this.currentAccount
          && this.currentAccount.id
          && this.currentAccount.id.value
        )
          ? this.currentAccount.id.value
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
