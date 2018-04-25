<template lang="pug">
  .collection-checkbox
    label.collection-checkbox-label(
      for='collection-checkbox-wrapper-checker-field')
    | {{ field.label }}
    .collection-checkbox-wrapper
      .collection-checkbox-wrapper-checker
        input.collection-checkbox-wrapper-checker-field(
          :id='`collection-checkbox-wrapper-checker-field-${field.id}`',
          type='checkbox',
          :checked='field.value',
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
    background-color: $ui-blue-dark;
    color: $ui-white-regular;
    height: 100%;
    left: 0;
    opacity: 0;
    padding: $ui-space-y/4;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 1;
  }

  .collection-checkbox-wrapper-checker-field {
    height: 100%;
    left: 0;
    opacity: 0;
    position: absolute;
    top: 0;
    transition: opacity 0.1s ease-in;
    width: 100%;
    z-index: 2;

    &:checked {
      + .collection-checkbox-wrapper-checker-icon {
        opacity: 1;
      }
    }
  }

  .collection-checkbox-wrapper-checker {
    border: 1px solid $ui-gray-light;
    height: 1.75rem;
    max-width: 1.75rem;
    margin-top: $ui-space-y/4;
    position: relative;
    width: 100%;
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
