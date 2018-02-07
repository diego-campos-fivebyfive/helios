<template lang="pug">
form
  fieldset.fields
    label.half
      | Nome
      input(placeholder='Nome')
    label.half
      | Valor
      input(placeholder='Valor')
    label.full
      | Conta
      select
        option Conta
</template>

<script>

</script>

<style lang="scss" scoped>
  .fields {
    label {
      float: left;
      font-weight: bold;
      margin: $ui-space-y/2 $ui-space-x/2;
      width: 100%;
      color: $ui-gray-dark;
    }

    input,
    select {
      border: 1px solid $ui-gray-light;
      border-radius: 1px;
      color: $ui-gray-dark;
      display: block;
      height: $ui-action-y;
      margin-top: $ui-space-y/2;
      width: 100%;
      transition:
        border-color 150ms ease-in-out 0s,
        box-shadow 150ms ease-in-out 0s;

      &:focus {
        border-color: $ui-blue-light;
      }

      &::-webkit-input-placeholder {
        color: $ui-gray-regular;
      }

      &::-moz-placeholder {
        color: $ui-gray-regular;
      }

      &:-ms-input-placeholder {
        color: $ui-gray-regular;
      }
    }
  
    select {
      padding: 0 $ui-space-x/3;
    }
  
    input {
      padding: 0 $ui-space-x/2;
    }

    .full {
      max-width: calc(100% - #{$ui-space-x});
    }

    .half {
      max-width: calc(50% - #{$ui-space-x});
    }
  }
</style>
