<template lang="pug">
  Select(
    :label='field.label',
    :disabled='field.disabled.state',
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
        key: '',
        value: 'Não vinculado'
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
      this.axios.get('api/v1/account/levels')
        .then(response => {
          const levels = response.data

          this.options = Object.entries(levels)
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
