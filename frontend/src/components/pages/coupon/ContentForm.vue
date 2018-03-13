<template lang="pug">
  ModalForm(ref='modalForm')
    form(slot='section', name='coupon')
      fieldset.fields
        label.half
          | Nome
          input(
            v-model='form.payload.name',
            placeholder='Nome')
        label.half
          | Valor
          input(
            v-model='form.payload.amount',
            placeholder='Valor',
            v-on:blur='isValidAmount')
        label.full
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

        this.$refs.modalForm.notify('Formato de moeda em Real invalido')
        this.form.resolved = true
      },
      show(coupon) {
        this.$refs.modalForm.show()

        if (coupon) {
          this.form.action = 'edit'
          this.form.payload = Object.assign({}, coupon)
          return
        }

        this.form.action = 'create'
        this.form.payload = Object.assign({}, this.form.default)
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
  /* ContentForm Style */
</style>
