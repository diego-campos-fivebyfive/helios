<template lang="pug">
  Field(
    ref='field',
    :field='field')
    input.field(
      :value='field.value',
      :placeholder='placeholder',
      v-on:blur='$emit("validate", field)',
      v-on:input='$set(field, "value", $event.target.value)')
</template>

<script>
  import Field from 'theme/collection/Field'

  export default {
    name: 'Check',
    components: {
      Field
    },
    props: {
      field: {
        type: Object,
        required: true
      }
    },
    data: () => ({
      placeholder: ''
    }),
    mounted() {
      this.setInputPlaceholder()
    },
    methods: {
      setInputPlaceholder() {
        this.placeholder = this.$refs.field.getPlaceholder()
      }
    }
  }
</script>

<style lang="scss" scoped>
  /* Check Style */
</style>
