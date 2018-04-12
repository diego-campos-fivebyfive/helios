<template lang="pug">
  label
    | {{ field.label }}
    input(
      :value='field.value',
      :placeholder='field.placeholder || field.label',
      v-on:input='$set(field, "value", $event.target.value)')
</template>

<script>
  export default {
    props: [
      'field'
    ]
  }
</script>

<style lang="scss" scoped>
  label {
    float: left;
    font-weight: 600;
    padding: $ui-space-y/2 $ui-space-x/2;
  }

  input {
    background-color: $ui-white-regular;
    border: 1px solid $ui-gray-light;
    border-radius: 1px;
    color: $ui-gray-dark;
    display: block;
    height: $ui-action-y;
    padding: 0 $ui-space-x/2;
    margin-top: $ui-space-y/2;
    width: 100%;
    transition:
      border-color 150ms ease-in-out 0s,
      box-shadow 150ms ease-in-out 0s;

    @include placeholder-color($ui-gray-regular);

    &:focus {
      border-color: $ui-blue-light;
    }
  }
</style>
