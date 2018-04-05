<template lang="pug">
  label
    | {{ label }}
    select(v-on:change='updateOption($event.target.value)')
      option(
        v-for='option in options',
        :selected='selected === option.value',
        :value='option.value')
        | {{ option.text }}
</template>

<script>
  export default {
    props: [
      'label',
      'options',
      'selected'
    ],
    methods: {
      updateOption(selectedOption) {
        const option = this.options.find(currentOption => (
          String(currentOption.value) === selectedOption
        ))
        this.$emit('update', option)
      }
    }
  }
</script>

<style lang="scss">
  /* Select Style */
</style>
