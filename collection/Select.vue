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
      field: {
        type: Object,
        required: true
      },
      disabled: {
        type: Boolean,
        required: false,
        default: false
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
      updateOption(selectedOption) {
        const currentOption = this.options.find(eachOption => (
          String(eachOption.value) === selectedOption
        ))

        this.$emit('update', currentOption)
      },
      selectedValue(option) {
        return this.selected === option.value
      }
    }
  }
</script>

<style lang="scss" scoped>
  /* Select Style */
</style>
