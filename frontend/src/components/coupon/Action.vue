<template lang="pug">
form
  fieldset.fields
    label.half
      | Nome
      input(
        placeholder='Nome',
        v-model='coupon.name')
    label.half
      | Valor
      input(
        placeholder='Valor',
        v-model='coupon.amount')
    label.full
      | Conta
      select(v-model='coupon.account')
        option(value='') Não vinculada
        option(
          v-for='account in accounts',
          :value='account.id')
            | {{ account.name }}
</template>

<script>
  export default {
    data: () => ({
      coupon: {
        name: '',
        amount: '',
        account: ''
      },
      accounts: []
    }),
    mounted() {
      const uri = `api/v1/account/available`
      this.axios.get(uri).then(response => {
        this.accounts = response.data
      })
    },
    methods: {
      send() {
        this.axios.post('api/v1/coupon/', this.coupon)
      }
    }
  }
</script>

<style lang="scss" scoped>
  .fields {
    label {
      float: left;
      font-weight: 600;
      margin: $ui-space-y/2 $ui-space-x/2;
      width: 100%;
    }

    select {
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
