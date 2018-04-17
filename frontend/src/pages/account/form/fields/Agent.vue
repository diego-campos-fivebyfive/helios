<template lang="pug">
  Select(
    :label='field.label',
    :disabled='field.disabled.state',
    :options='options',
    :selected='getCurrentAgent',
    v-on:update='updateAgent')
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
      updateAgent(select) {
        this.$set(this.field, 'value', select.value)
      }
    },
    computed: {
      getCurrentAgent() {
        return (
          this.field.value
          && this.field.value.id
        )
          ? this.field.value.id
          : this.defaultOption.value
      }
    },
    mounted() {
      this.axios.get('api/v1/account/agents/2209')
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
