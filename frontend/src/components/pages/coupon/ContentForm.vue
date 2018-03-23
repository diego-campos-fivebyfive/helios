<template lang="pug">
  ModalForm(ref='modalForm')
    h1.title(slot='header')
      | {{ form.title }}
    form.form(slot='section', name='coupon')
      label.field-name
        | Nome
        input(
          v-model='form.payload.name.value',
          placeholder='Nome')
      label.field-value
        | Valor
        input(
          v-model='form.payload.amount.value',
          placeholder='Valor',
          v-on:blur='validate(form.payload.amount)')
      label.field-account
        | Conta
        SelectAccountForm(
          v-model.sync='form.payload.account',
          :currentAccount='form.payload.account')
    ActionForm(
      slot='buttons',
      :action='form.action',
      :getPayload='() => $refs.modalForm.getPayload(form.payload)',
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
        title: '',
        payload: {
          id: {
            default: null
          },
          name: {
            default: ''
          },
          amount: {
            default: null,
            type: 'money',
            exception: 'Formato de moeda inválido',
            resolved: false
          },
          account: {
            id: {
              default: null
            },
            name: {
              default: ''
            }
          }
        }
      }
    }),
    methods: {
      validate(field) {
        return this.$refs.modalForm.validateField(field)
      },
      show(coupon) {
        const { assignPayload, show } = this.$refs.modalForm

        show()

        if (coupon) {
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
            this.$refs.modalForm.notify(message, 'common-success')
          })
          .catch(message => {
            this.$refs.modalForm.notify(message, 'common-warning')

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
