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
      this.axios.get('api/v1/account/states')
        .then(response => {
          const states = response.data

          this.options = Object.entries(states)
            .map(item => ({
              value: item[0],
              text: item[1]
            }))
        })
    }
  }
</script>

<style lang="scss">
  /* ContentForm Style */
</style>
