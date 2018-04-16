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
  .collection-checkbox {
    float: left;
    padding: $ui-space-y/2 $ui-space-x/2;

    &:hover {
      border-color: $ui-blue-dark;
    }
  }

  .collection-checkbox-wrapper {
    display: flex;
    margin-top: $ui-space-y/2;
  }

  .collection-checkbox-wrapper-checker {
    border: 1px solid $ui-gray-light;
    height: 1.75rem;
    width: 100%;
    max-width: 1.75rem;
    margin-top: 0.25rem;
    position: relative;
  }

  .collection-checkbox-wrapper-checker-field {
    display: none;
    transition: opacity 0.1s ease-in;

    &:checked {
      + .collection-checkbox-wrapper-checker-icon {
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

  .collection-checkbox-wrapper-checker-icon {
    opacity: 0;
  }

  .collection-checkbox-wrapper-description {
    font-weight: 700;
    margin-left: $ui-space-y/2;
  }
</style>
