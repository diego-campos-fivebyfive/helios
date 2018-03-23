<template lang="pug">
  ModalForm(ref='modalForm')
    h1.title(slot='header')
      | {{ form.title }}
    form.form(slot='section', name='coupon')
      label.field-name
        | Nome
        input(
          v-model='form.payload.name',
          placeholder='Nome')
      label.field-value
        | Valor
        input(
          v-model='form.payload.amount',
          placeholder='Valor',
          v-on:blur='isValidAmount')
      label.field-account
        | Conta
        SelectAccountForm(
          v-model.sync='form.payload.account',
          :currentAccount='form.payload.account')
    ActionForm(
      slot='buttons',
      :action='form.action',
      :payload='form.payload',
      :resolved='form.resolved',
      v-on:done='done')
</template>

<script>
  import ActionForm from './ActionForm'
  import SelectAccountForm from './SelectAccountForm'

  export default {
    components: {
      ActionForm,
      SelectAccountForm
    },
    data: () => ({
      form: {
        action: '',
        default: {
          name: '',
          amount: null,
          account: {}
        },
        payload: {},
        resolved: false
      }
    }),
    methods: {
      isValidAmount() {
        if (
          /^(\d{1,3}(\.\d{3})*|\d+)(\,\d{2})?$/.test(this.form.payload.amount)
        ) {
          this.form.resolved = false
          return
        }

        this.$refs.modalForm.notify('Formato de moeda em Real invalido', 'warning')
        this.form.resolved = true
      },
      show(coupon) {
        this.$refs.modalForm.show()

        if (coupon) {
          this.form.action = 'edit'
          this.form.title = 'Edição de Cupom'
          this.form.payload = Object.assign({}, coupon)
          return
        }

        this.form.action = 'create'
        this.form.title = 'Cadastro de Cupom'
        this.form.payload = Object.assign({}, this.form.default)
      },
      done(response) {
        this.$refs.modalForm.hide()

        response
          .then(message => {
            this.$emit('getCoupons')
            this.$refs.modalForm.notify(message, 'success')
          })
          .catch(message => {
            this.$refs.modalForm.notify(message, 'warning')
          })
      }
    }
  }
</script>

<style lang="scss">
  $field-base-size: $ui-size-sm - $ui-space-x*2;
  $form-cols: 2;

  $col-size: get-col-size($field-base-size, $form-cols);

  .field-name {
    flex: 1 1 get-field-size($col-size, 1);
  }

  .field-value {
    flex: 1 1 get-field-size($col-size, 1);
  }

  .field-account {
    flex: 1 1 get-field-size($col-size, 2);
  }
</style>
