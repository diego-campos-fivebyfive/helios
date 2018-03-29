<template lang="pug">
  ModalForm(ref='modalForm')
    h1.title(slot='header')
      | {{ form.title }}
    form.form(slot='section', name='coupon')
      Input.field-name(
        label='Nome',
        :params='form.payload.name',
        v-model.sync='form.payload.name.value')
      Input.field-amount(
        label='Valor',
        :params='form.payload.amount',
        v-model.sync='form.payload.amount.value',
        v-on:validate='() => validate("form.payload.amount")')
      label.field-account
        | Conta
        AccountSelect(
          v-model.sync='form.payload.account',
          :currentAccount='form.payload.account')
    Actions(
      slot='buttons',
      :action='form.action',
      :getPayload='() => $refs.modalForm.getPayload(form.payload)',
      v-on:done='done')
</template>

<script>
  import Actions from './Actions'
  import AccountSelect from 'application/select/Accounts'

  export default {
    components: {
      Actions,
      AccountSelect
    },
    data: () => ({
      form: {
        action: '',
        title: '',
        payload: {
          account: {
            id: {},
            name: {}
          },
          amount: {
            type: 'money',
            exception: 'Formato de moeda inválido'
          },
          id: {},
          name: {}
        }
      }
    }),
    methods: {
      validate(path) {
        const { getPayloadField, isInvalidField } = this.$refs.modalForm

        const field = getPayloadField(this, path)
        field.rejected = isInvalidField(field)
      },
      show(coupon) {
        const { assignPayload, show } = this.$refs.modalForm

        show()

        if (Object.keys(coupon).length > 0) {
          this.form.action = 'edit'
          this.form.title = 'Edição de Cupom'
          this.form.payload = assignPayload(this.form.payload, coupon)
          return
        }

        this.form.action = 'create'
        this.form.title = 'Cadastro de Cupom'
        this.form.payload = assignPayload(this.form.payload, {})
      },
      done(response) {
        const { hide, notify } = this.$refs.modalForm

        hide()

        response
          .then(message => {
            this.$emit('getCoupons')
            notify(message, 'primary-common')
          })
          .catch(message => {
            notify(message, 'danger-common')
          })
      }
    }
  }
</script>

<style lang="scss" scoped>
  $field-base-size: $ui-size-sm - $ui-space-x*2;
  $form-cols: 2;

  $col-size: get-col-size($field-base-size, $form-cols);

  .field-name {
    flex: 1 1 get-field-size($col-size, 1);
  }

  .field-amount {
    flex: 1 1 get-field-size($col-size, 1);
  }

  .field-account {
    flex: 1 1 get-field-size($col-size, 2);
  }
</style>
