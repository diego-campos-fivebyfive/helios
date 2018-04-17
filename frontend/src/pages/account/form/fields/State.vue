<template lang="pug">
  Select(
    :label='field.label',
    :options='options',
    :selected='getCurrentState',
    v-on:update='updateState')
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
    props: [
      'field'
    ],
    methods: {
      updateState(select) {
        this.$set(this.field, 'value', select.value)
      }
    },
    computed: {
      getCurrentState() {
        return (
          this.field.value
          && this.field.value.id
        )
          ? this.field.value.id
          : null
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
