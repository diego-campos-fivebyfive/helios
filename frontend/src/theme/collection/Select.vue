<template lang="pug">
  Field(
    :field='field')
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
    props: {
      field: {
        type: Object,
        required: true
      },
      disabled: {
        type: Boolean,
        required: true
      },
      options: {
        type: Array,
        required: true
      },
      selected: {
        type: Object,
        required: true
      }
    },
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
