<template lang="pug">
  div
    Notification(ref='notification')
    Modal(ref='modal')
      h1.title(slot='header')
      | Novo Cupom
      form(slot='section', ref='send', name='coupon')
        fieldset.fields
          label.half
            | Nome
            input(
              v-model='form.name',
              placeholder='Nome')
          label.half
            | Valor
            input(
              v-model='form.amount',
              placeholder='Valor')
          label.full
            | Conta
            Select(
              v-model='form.account',
              :selected='form.account.id',
              :options='options.accounts',
              v-on:update='updateAccount')
      Button(
        slot='buttons',
        icon='save',
        type='primary-strong',
        label='Salvar',
        pos='single',
        v-on:click.native='sendCoupon')
</template>

<script>
  export default {
    data: () => ({
      options: {
        accounts: []
      },
      form: {
        name: '',
        amount: null,
        account: {}
      },
      modal: {
        action: ''
      }
    }),
    methods: {
      updateAccount(account) {
        this.form.account = {
          id: account.value,
          name: account.text
        }
      },
      createCoupon() {
        this.axios.post('api/v1/coupon/', this.form)
          .then(() => {
            this.$emit('getCoupons')
            this.$refs.notification.notify('Cupom cadastrado com sucesso')
          })
          .catch(() => {
            this.$refs.notification.notify('Não foi possível cadastrar cupom')
          })
      },
      editCoupon() {
        this.axios.put(`api/v1/coupon/${this.form.id}`, this.form)
          .then(() => {
            this.$emit('getCoupons')
            this.$refs.notification.notify('Cupom editado com sucesso')
          })
          .catch(() => {
            this.$refs.notification.notify('Não foi possível editar cupom')
          })
      },
      sendCoupon() {
        if (!/^(\d{1,3}(\.\d{3})*|\d+)(\,\d{2})?$/.test(this.form.amount)) {
            this.$refs.notification.notify('Formato de valor invalido')
            return
        }

        this.$refs.modal.hide()

        if (this.modal.action === 'create') {
          this.createCoupon()
          return
        }

        this.editCoupon()
      },
      showActionModal(action, coupon) {
        if (coupon) {
          this.form = coupon
        }

        this.modal.action = action
        this.$refs.modal.show()
      }
    },
    mounted() {
      this.axios.get('api/v1/account/available')
        .then(response => {
          const defaultOption = {
            id: '',
            name: 'Não vinculada'
          }

          const accounts = response.data
          accounts.unshift(defaultOption)

          this.options.accounts = accounts.map(account => ({
            value: account.id,
            text: account.name
          }))
        })
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
