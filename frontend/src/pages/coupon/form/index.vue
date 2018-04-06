<template lang="pug">
  Form(ref='modalForm', modal)
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
      :getPayload='() => $refs.modalForm.getPayload(form.payload)',
      v-on:done='done')
</template>

<script>
  import Actions from './Actions'

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
