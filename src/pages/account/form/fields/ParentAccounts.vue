<template lang="pug">
  Select(
    :field='field',
    :options='options',
    :selected='getCurrentParentAccount',
    v-on:update='updateParentAccount')
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
    props: {
      field: {
        type: Object,
        required: true
      }
    },
    methods: {
      setDisabledState(select) {
        this.$emit('disableFields', {
          state: (select.value !== this.defaultOption.value),
          manager: this.field.schemaID
        })
      },
      updateParentAccount(select) {
        this.setDisabledState(select)

        this.$set(this.field, 'value', {
          id: select.value,
          name: select.text
        })
      }
    },
    computed: {
      getCurrentParentAccount() {
        return (
          this.field.value
          && this.field.value.id
        )
          ? this.field.value.id
          : this.defaultOption.value
      }
    },
    mounted() {
      this.axios.get('api/v1/account/parent_accounts/2209')
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
