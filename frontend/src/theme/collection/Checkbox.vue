<template lang="pug">
  .collection-checkbox
    label.collection-checkbox-label(
      for='collection-checkbox-wrapper-checker-field')
    | {{ field.label }}
    .collection-checkbox-wrapper
      .collection-checkbox-wrapper-checker
        input.collection-checkbox-wrapper-checker-field(
          id='collection-checkbox-wrapper-checker-field',
          type='checkbox',
          :value='field.value',
          v-on:change='$set(field, "value", !field.value)')
        Icon.collection-checkbox-wrapper-checker-icon(name='check')
      .collection-checkbox-wrapper-description(
        v-if='field.description')
        | {{ field.description }}
</template>

<script>
  export default {
    props: [
      'field'
    ]
  }
</script>

<style lang="scss" scoped>
  .collection-checkbox-wrapper-checker-icon {
    opacity: 0;
    background-color: $ui-blue-dark;
    color: $ui-white-regular;
    height: 100%;
    left: 0;
    padding: $ui-space-y/4;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 1;
  }

  .collection-checkbox-wrapper-checker-field {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 2;
    transition: opacity 0.1s ease-in;
    opacity: 0;

    &:checked {
      + .collection-checkbox-wrapper-checker-icon {
        opacity: 1;
      }
    }
  }

  .collection-checkbox-wrapper-checker {
    border: 1px solid $ui-gray-light;
    height: 1.75rem;
    width: 100%;
    max-width: 1.75rem;
    margin-top: $ui-space-y/4;
    position: relative;
  }

  .collection-checkbox-wrapper-description {
    color: $ui-gray-dark;
    font-weight: 600;
    margin-left: $ui-space-y/2;
  }

  .collection-checkbox-wrapper {
    display: flex;
    margin-top: $ui-space-y/2;
  }

  .collection-checkbox {
    padding: $ui-space-y/2 $ui-space-x/2;

    &:hover {
      .collection-checkbox-wrapper-checker {
        border-color: $ui-blue-dark;
      }
    }
  }
</style>
