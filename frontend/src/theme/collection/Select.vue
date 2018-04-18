<template lang="pug">
  Field(
    :label='label'
    :field='options')
    select.field(
      :disabled='disabled',
      v-on:change='updateOption($event.target.value)')
      option(
        v-for='option in options',
        :selected='selected === option.value',
        :value='option.value')
        | {{ option.text }}
</template>

<script>
  import Field from '@/theme/collection/Field'

  export default {
    components: {
      Field
    },
    props: [
      'label',
      'disabled',
      'options',
      'selected'
    ],
    methods: {
      updateOption(selectedOption) {
        const currentOption = this.options.find(eachOption => (
          String(eachOption.value) === selectedOption
        ))

        this.$emit('update', currentOption)
      }
    }
  }
</script>

<style lang="scss" scoped>
  /* Select Style */
</style>
