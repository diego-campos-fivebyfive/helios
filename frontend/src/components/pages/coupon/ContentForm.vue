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
            placeholder='Valor')
        label.full
          | Conta
          SelectAccountForm(
            v-model.sync='form.payload.account',
            :currentAccount='form.payload.account')
    ActionForm(
      slot='buttons',
      icon='save',
      type='primary-strong',
      label='Salvar',
      pos='single',
      v-on:click.native='send')
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
        default: {
          name: '',
          amount: null,
          account: {}
        },
        payload: {},
      }
    }),
    methods: {
      updateAccount(account) {
        this.form.payload.account = {
          id: account.value,
          name: account.text
        }
      },
      createCoupon() {
        this.axios.post('api/v1/coupon/', this.form.payload)
          .then(() => {
            this.$emit('getCoupons')
            this.$refs.modalForm.notify('Cupom cadastrado com sucesso')
          })
          .catch(() => {
            this.$refs.modalForm.notify('Não foi possível cadastrar cupom')
          })
      },
      editCoupon() {
        this.axios.put(`api/v1/coupon/${this.form.id}`, this.form.payload)
          .then(() => {
            this.$emit('getCoupons')
            this.$refs.modalForm.notify('Cupom editado com sucesso')
          })
          .catch(() => {
            this.$refs.modalForm.notify('Não foi possível editar cupom')
          })
      },
      send() {
        if (!/^(\d{1,3}(\.\d{3})*|\d+)(\,\d{2})?$/.test(this.form.payload.amount)) {
          this.$refs.modalForm.notify('Formato de moeda em Real invalido')
          return
        }

        this.$refs.modalForm.hide()

        if (this.modal.action === 'create') {
          this.createCoupon()
          return
        }

        this.editCoupon()
      },
      showModalForm(coupon) {
        if (coupon) {
          this.modal.action = 'edit'
          this.form.payload = Object.assign({}, coupon)
        } else {
          this.modal.action = 'create'
          this.from.payload = this.from.default
        }

        this.$refs.modalForm.show()
      }
    }
  }
</script>

<style lang="scss">
  /* ContentForm Style */
</style>
