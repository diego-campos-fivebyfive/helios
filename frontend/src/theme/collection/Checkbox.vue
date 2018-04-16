<template lang="pug">
  label.collection-checkbox
    | {{ field.label }}
    input.collection-checkbox-field(
      type='checkbox',
      :value='field.value',
      v-on:input='$set(field, "value", $event.target.value)')
    Icon.collection-checkbox-icon(name='check')
</template>

<script>
  export default {
    props: [
      'field'
    ]
  }
</script>

<style lang="scss" scoped>
  .collection-checkbox {
    float: left;
    font-weight: 600;
    padding: $ui-space-y/2 $ui-space-x/2;
    position: relative;
    max-width: 1.75rem;
    width: 100%;
    height: 1.75rem;
    border: 1px solid $ui-gray-light;

    &:hover {
      border-color: $ui-blue-dark;
    }
  }

  .collection-checkbox-field {
    display: none;
    transition: opacity 0.1s ease-in;

    &:checked {
      + .collection-checkbox-icon {
        background-color: $ui-blue-dark;
        color: $ui-white-regular;
        top: 0;
        left: 0;
        opacity: 1;
        position: absolute;
        padding: 0.25rem;
        width: 100%;
        height: 100%;
      }
    }
  }

  .collection-checkbox-icon {
    opacity: 0;
  }
</style>
