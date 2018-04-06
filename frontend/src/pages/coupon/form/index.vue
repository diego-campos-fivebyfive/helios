<template lang="pug">
  Form(ref='form', modal)
    h1.title(slot='header')
      | {{ form.title }}
    form.form(slot='section', name='coupon')
      component(
        v-for='field in form.payload',
        :key='field.name',
        :is='field.component',
        :label='field.label',
        :params='field',
        :class='"field-" + field.name',
        :updateField='updateField',
        :validateField='validateField')
    Actions(
      slot='buttons',
      :action='form.action',
      :getPayload='getFormPayload',
      v-on:done='done')
</template>

<script>
  import payload from '@/theme/validation/payload'

  import AccountSelect from '@/components/select/Accounts'
  import Input from '@/theme/collection/Input'
  import Actions from './Actions'

  const { assignPayload, getPayload, isInvalidField } = payload

  export default {
    components: {
      Actions
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
      update(name, value) {
        this.form.payload.map(field => {
          if (field.name === name) {
            this.$set(field, key, value)
          }
          return field
        })
      },
      validateField(params) {
        const { rejected, exception } = isInvalidField(params)

        this.updateField({
          name: params.name,
          key: 'rejected',
          value: rejected
        })

        if (rejected) {
          this.$refs.form.notify(exception, 'danger-common')
        }
      },
      getFormPayload() {
        getPayload(this.form.fields)
      },
      show(coupon) {
        const { show } = this.$refs.form

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
        const { hide, notify } = this.$refs.form

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
