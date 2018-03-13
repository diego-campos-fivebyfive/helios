<template lang="pug">
  Select(
    :selected='getAccountSelected',
    :options='options',
    v-on:update='updateAccount')
</template>

<script>
  export default {
    data: () => ({
      options: [],
      defaultOption: {
        id: '',
        name: 'Não vinculada'
      }
    }),
    props: [
      'currentAccount'
    ],
    methods: {
      updateAccount(account) {
        this.$emit('input', {
          id: account.value,
          name: account.text
        })
      }
    },
    computed: {
      getAccountSelected() {
        return (this.currentAccount && this.currentAccount.id)
          ? this.currentAccount.id
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
