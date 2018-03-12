<template lang="pug">
  div
    form(ref='send', name='coupon')
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
      send() {
        if (!/^(\d{1,3}(\.\d{3})*|\d+)(\,\d{2})?$/.test(this.form.amount)) {
          this.$refs.notification.notify('Formato de moeda em Real invalido')
          return
        }

        this.$refs.modalForm.hide()

        if (this.modal.action === 'create') {
          this.createCoupon()
          return
        }

        this.editCoupon()
      },
      setForm(action, coupon) {
        this.modal.action = action
        this.form = coupon
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

<style lang="scss">
  /* ContentForm Style */
</style>
