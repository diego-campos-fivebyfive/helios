<template lang="pug">
  Select(
    :field='field',
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
      options: []
    }),
    props: {
      field: {
        type: Object,
        required: true
      }
    },
    methods: {
      updateLevel(select) {
        this.$set(this.field, 'value', select.value)
      }
    },
    computed: {
      getCurrentLevel() {
        return (
          this.field.value
          && this.field.value.id
        )
          ? this.field.value.id
          : null
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
