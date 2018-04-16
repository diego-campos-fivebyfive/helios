<template lang="pug">
  label.collection-checkbox
    | {{ field.label }}
    .collection-checkbox-wrapper
      .collection-checkbox-wrapper-checker
        input.collection-checkbox-wrapper-checker-field(
          type='checkbox',
          :value='field.value',
          v-on:input='$set(field, "value", $event.target.value)')
        Icon.collection-checkbox-wrapper-checker-icon(name='check')
      .collection-checkbox-wrapper-description
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
  }

  .collection-checkbox-wrapper-checker-field {
    display: none;
    transition: opacity 0.1s ease-in;

    &:checked {
      + .collection-checkbox-wrapper-checker-icon {
        background-color: $ui-blue-dark;
        color: $ui-white-regular;
        height: 100%;
        left: 0;
        opacity: 1;
        padding: 0.25rem;
        position: absolute;
        top: 0;
        width: 100%;
      }
    }
  }

  .collection-checkbox-wrapper-checker {
    border: 1px solid $ui-gray-light;
    height: 1.75rem;
    width: 100%;
    max-width: 1.75rem;
    margin-top: 0.25rem;
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
    float: left;
    padding: $ui-space-y/2 $ui-space-x/2;

    &:hover {
      .collection-checkbox-wrapper-checker {
        border-color: $ui-blue-dark;
      }
    }
  }
</style>
