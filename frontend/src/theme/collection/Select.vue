<template lang="pug">
  label
    | {{ label }}
    select(
      :disabled='disabled',
      v-on:change='updateOption($event.target.value)')
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
  label {
    float: left;
    font-weight: 600;
    padding: $ui-space-y/2 $ui-space-x/2;
  }

  select {
    background-color: $ui-white-regular;
    border: 1px solid $ui-gray-light;
    border-radius: 1px;
    color: $ui-gray-dark;
    display: block;
    height: $ui-action-y;
    padding: 0 $ui-space-x/3;
    margin-top: $ui-space-y/2;
    width: 100%;
    transition:
      border-color 150ms ease-in-out 0s,
      box-shadow 150ms ease-in-out 0s;

    &:focus {
      border-color: $ui-blue-light;
    }

    &:disabled {
      background-color: $ui-gray-light;
      cursor: not-allowed;
    }
  }
</style>
