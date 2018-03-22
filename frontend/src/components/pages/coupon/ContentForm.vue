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
          v-on:blur='validateField(form.payload.amount)')
      label.field-account
        | Conta
        SelectAccountForm(
          v-model.sync='form.payload.account',
          :currentAccount='form.payload.account')
    ActionForm(
      slot='buttons',
      :action='form.action',
      :getPayload='getPayload',
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
      validateField(field) {
        const patterns = {
          'money': /^(\d{1,3}(\.\d{3})*|\d+)(\,\d{2})?$/
        }

        const pattern = patterns[field.type]

        const exceptions = {
          'money': 'Formato de moeda em Real invalido'
        }

        const defaultException = exceptions[field.type]


        if (pattern.test(field.value)) {
          field.resolved = true
          return true
        }

        this.$refs.modalForm.notify(field.exception || defaultException)
        field.resolved = false
        return false
      },
      isValidPayload(payload) {
        const isResolved = (obj, key) => {
          const val = obj[key]

          if (val === Object(val)) {
            return isValid(val)
          }

          return (key === 'resolved' && !val)
            ? this.validateField(obj)
            : true
        }

        const isValid = obj => {
          for (let key in obj) {
            if (!isResolved(obj, key)) return false
          }

          return true
        }

        return isValid(payload)
      },
      getPayload() {
        if (!this.isValidPayload(this.form.payload)) {
          return false
        }

        return this.formatPayload(this.form.payload)
      },
      formatPayload(payload) {
        const format = obj =>
          Object
            .entries(obj)
            .reduce((acc, [key, val]) => {
              acc[key] = val.hasOwnProperty('value')
                ? val.value
                : format(val)
              return acc
            }, {})

        return format(payload)
      },
      assign(base, data = {}) {
        const assign = (base, data = {}) =>
          Object
            .entries(base)
            .reduce((acc, [key, val]) => {
              if (val === Object(val)) {
                acc[key] = assign(val, data[key] || '')
                return acc
              }

              if (key === 'default') {
                acc['value'] = data || val
              }

              acc[key] = (key === 'resolved') ? false : val
              return acc
            }, {})

        return assign(base, data)
      },
      show(coupon) {
        this.$refs.modalForm.show()

        if (coupon) {
          this.form.action = 'edit'
          this.form.title = 'Edição de Cupom'
          this.form.payload = this.assign(this.form.payload, coupon)
          return
        }

        this.form.action = 'create'
        this.form.title = 'Cadastro de Cupom'
        this.form.payload = this.assign(this.form.payload, {})
      },
      done(response) {
        this.$refs.modalForm.hide()

        response
          .then(message => {
            this.$emit('getCoupons')
            this.$refs.modalForm.notify(message)
          })
          .catch(message => {
            this.$refs.modalForm.notify(message)
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
