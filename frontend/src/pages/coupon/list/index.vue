<template lang="pug">
  div
    Notification(ref='notification')
    Confirm(ref='confirm', v-on:removeItem='removeCoupon')
      div(slot='content')
        Icon.icon(name='question-circle-o', scale='4')
        h2
        | Confirma exclusão deste Cupom?
    Table.table(type='stripped')
      tr(slot='head')
        th.col-name Nome
        th.col-code Código
        th.col-account Conta
        th.col-status Status
        th.col-amount Valor
        th.col-action Ações
      tr.rows(slot='rows', v-for='coupon in coupons')
        td.col-name {{ coupon.name }}
        td.col-code {{ coupon.code }}
        td.col-account {{ coupon.account.name || 'Não Vinculada' }}
        td.col-status {{ coupon.applied ? 'Aplicado' : 'Não Aplicado' }}
        td.col-amount R$ {{ coupon.amount }}
        td.col-action
          Button(
            v-if='!coupon.applied',
            type='primary-common',
            icon='pencil',
            pos='first',
            v-on:click.native='$emit("show", "edit", coupon)')
          Button(
            v-if='!coupon.applied',
            type='danger-common',
            icon='trash',
            pos='last',
            v-on:click.native='$refs.confirm.show(coupon.id)')
</template>

<script>
  export default {
    props: [
      'coupons'
    ],
    methods: {
      removeCoupon(id) {
        this.$refs.confirm.hide()

        this.axios.delete(`/api/v1/coupon/${id}`)
          .then(() => {
            this.$emit('getCoupons')
            this.$refs.notification.notify('Cupom removido com sucesso')
          })
      }
    }
  }
</script>

<style lang="scss" scoped>
  .rows {
    cursor: pointer;
  }

  .col-name {
    min-width: 20%;
    text-align: left;
  }

  .col-code {
    text-align: center;
  }

  .col-account {
    min-width: 20%;
    text-align: center;
  }

  .col-status {
    text-align: center;
  }

  .col-amount {
    text-align: right;
  }

  .col-action {
    text-align: right;
  }
</style>
