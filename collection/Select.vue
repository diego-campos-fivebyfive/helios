<template lang="pug">
  Field(
    :field='field')
    select.field(
      :disabled='disabled',
      v-on:change='updateOption($event.target.value)')
      option(
        v-for='option in options',
        :selected='selectedValue(option)',
        :value='option.value')
        | {{ option.text }}
</template>

<script>
  import Field from 'theme/collection/Field'

  export default {
    components: {
      Field
    },
    props: {
      disabled: {
        type: Boolean,
        required: false,
        default: false
      },
      field: {
        type: Object,
        required: true
      },
      options: {
        type: Array,
        required: true
      },
      selected: {
        type: [
          Number,
          Boolean
        ],
        required: false
      }
    },
    methods: {
      selectedValue(option) {
        return this.selected === option.value
      },
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
