<template lang="pug">
  SelectSearch(
    :field='field',
    :options='options',
    :selected='selected',
    v-on:request='requestAccount',
    v-on:update='updateAccount')
</template>

<script>
  import SelectSearch from '@/theme/collection/SelectSearch'

  export default {
    components: {
      SelectSearch
    },
    data: () => ({
      selected: {},
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
      requestAccount(search = '') {
        this.selected = {
          value: '',
          text: search
        }

        this.axios.get(`api/v1/account/available?search=${search}`)
          .then(response => {
            const accounts = response.data

            this.options = accounts
              .map(this.formatOption)

            this.options.unshift(this.defaultOption)
          })
      },
      updateAccount(select = {}) {
        const id = select.value || this.defaultOption.value
        const name = select.text || this.defaultOption.text
        this.$set(this.field, 'value', { id, name })

        this.setCurrentAccount()
      },
      formatOption(option) {
        return {
          value: option.id,
          text: option.name
        }
      },
      setCurrentAccount() {
        this.selected = (
          this.field.value
          && this.field.value.id
        )
          ? this.formatOption(this.field.value)
          : this.defaultOption
      }
    },
    mounted() {
      this.requestAccount()
      this.setCurrentAccount()
    }
  }
</script>

<style lang="scss">
  /* ContentForm Style */
</style>
