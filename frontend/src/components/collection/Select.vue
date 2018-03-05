<template lang="pug">
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
      'selected',
      'options'
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
