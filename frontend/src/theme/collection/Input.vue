<template lang="pug">
  label(:class='{ "danger-common": field.rejected }')
    | {{ field.label }}
    input(
      :value='field.value',
      :placeholder='field.placeholder || field.label',
      v-on:input='$set(field, "value", $event.target.value)')
    Icon.icon(
      v-if='field.rejected',
      name='info')
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
