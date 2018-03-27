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
      label.field-value(
        :class='{ "danger-common": form.payload.amount.rejected }')
        | Valor
        input(
          v-model='form.payload.amount.value',
          placeholder='Valor',
          v-on:blur='validate("form.payload.amount")')
        Icon.icon(v-if="form.payload.amount.rejected", name="info-circle", class="danger-common")
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
            exception: 'Formato de moeda inválido',
            rejected: false
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

<style lang="scss">
  $field-base-size: $ui-size-sm - $ui-space-x*2;
  $form-cols: 2;

  $col-size: get-col-size($field-base-size, $form-cols);

  .field-name {
    flex: 1 1 get-field-size($col-size, 1);
  }

  .field-value {
    flex: 1 1 get-field-size($col-size, 1);
    position: relative;
  }

  .field-account {
    flex: 1 1 get-field-size($col-size, 2);
  }

  .form {
    .danger-common {
      color: $ui-red-dark !important;
      opacity: 0.8;

      input {
        border-color: $ui-red-lighter !important;
        color: inherit !important;
      }
    }

    .icon {
      position: absolute;
      right: 2em;
      bottom: 1.25em;
      color: $ui-red-lighter;
    }
  }
</style>
