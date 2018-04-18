<template lang="pug">
  Field(
    :field='field',
    :label='field.label')
    input.field(
      :value='field.value',
      :placeholder='field.placeholder || field.label',
      v-on:blur='$emit("validate", field)',
      v-on:input='$set(field, "value", $event.target.value)')
</template>

<script>
  import Field from '@/theme/collection/Field'

  export default {
    components: {
      Field
    },
    props: [
      'field'
    ]
  }
</script>

<style lang="scss" scoped>
  /* Check Style */
</style>
