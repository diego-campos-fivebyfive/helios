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
    Button(
      type='primary-strong',
      label='Salvar',
      pos='single',
      v-on:click.native='send')
</template>

<script>
  export default {
    methods: {
      send() {
        this.axios.post('/coupon/api/', {
          name: 'a',
          amount: 10,
          account: 19
        })
      }
    }
  }
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

    select {
      color: $ui-gray-regular;
      padding: 0 $ui-space-x/3;
    }

    input {
      padding: 0 $ui-space-x/2;

      @include placeholder-color($ui-gray-regular);
    }

    input,
    select {
      background-color: $ui-white-regular;
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
    }

    .full {
      max-width: calc(100% - #{$ui-space-x});
    }

    .half {
      max-width: calc(50% - #{$ui-space-x});
    }
  }
</style>
