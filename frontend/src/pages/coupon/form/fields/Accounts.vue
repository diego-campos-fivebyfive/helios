<template lang="pug">
  Select(
    :label='field.label',
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
        value: '',
        text: 'Não vinculada'
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
          : this.defaultOption.value
      }
    },
    mounted() {
      this.axios.get('api/v1/account/available')
        .then(response => {
          const accounts = response.data

          this.options = accounts
            .map(account => ({
              value: account.id,
              text: account.name
            }))

          this.options.unshift(this.defaultOption)
        })
    }
  }
</script>

<style lang="scss">
  /* ContentForm Style */
</style>
