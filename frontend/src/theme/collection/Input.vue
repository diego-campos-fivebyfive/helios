<template lang="pug">
  label(:class='{ "danger-common": params.rejected }')
    | {{ label }}
    input(
      :value='params.value',
      :placeholder='placeholder || label',
      v-on:blur='$emit("validate")',
      v-on:input='$emit("input", $event.target.value)')
    Icon.icon(
      v-if='params.rejected',
      name='info')
</template>

<script>
  export default {
    props: [
      'label',
      'params',
      'placeholder'
    ]
  }
</script>

<style lang="scss" scoped>
  .danger-common {
    color: $ui-red-dark;
    opacity: 0.75;
    position: relative;

    input {
      border-color: $ui-red-lighter;
      color: inherit;
    }
  }

  .icon {
    border: 1px solid $ui-red-dark;
    border-radius: 100%;
    bottom: $ui-space-y;
    height: 1.5rem;
    padding: 0.25rem;
    position: absolute;
    right: $ui-space-x;
    width: 1.5rem;
  }
</style>
