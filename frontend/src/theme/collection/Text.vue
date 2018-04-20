<template lang="pug">
  Field(
    ref='field',
    :field='field')
    input.field(
      :value='field.value',
      :placeholder='placeholder',
      v-on:input='$set(field, "value", $event.target.value)')
</template>

<script>
  import Field from '@/theme/collection/Field'

  export default {
    data: () => ({
      placeholder: ''
    }),
    components: {
      Field
    },
    props: [
      'field'
    ],
    mounted() {
      this.placeholder = this.$refs.field.getPlaceholder()
    }
  }
</script>

<style lang="scss" scoped>
  /* Text Style */
</style>
