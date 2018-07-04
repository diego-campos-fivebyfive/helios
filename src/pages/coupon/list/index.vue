<template lang="pug">
  div
    Notification(ref='notification')
    Confirm(ref='confirm', v-on:removeItem='removeCoupon')
      div(slot='content')
        Icon.icon(name='question-circle-o', scale='4')
        h2.title Confirma exclusão deste Cupom?
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
        td.col-account {{ coupon.account.nameText }}
        td.col-status {{ coupon.appliedText }}
        td.col-amount R$ {{ coupon.amount }}
        td.col-action
          nav(v-if='showOptions(coupon)')
            Button(
              class='primary-common',
              pos='first',
              :action='() => $emit("showUpdateForm", coupon)')
              Icon(name='pencil')
            Button(
              class='danger-common',
              pos='last',
              :action='() => $refs.confirm.show(coupon.id)')
              Icon(name='trash')
</template>

<script>
  export default {
    props: {
      coupons: {
        type: Array,
        required: true
      }
    },
    methods: {
      removeCoupon(id) {
        this.$refs.confirm.hide()

        this.axios.delete(`/api/v1/coupon/${id}`)
          .then(() => {
            this.$emit('getCoupons')
            this.$refs.notification.notify('Cupom removido com sucesso')
          })
      },
      showOptions(coupon) {
        return !coupon.applied
      }
    },
    watch: {
      coupons() {
        this.coupons.map(coupon => (
          Object.assign(coupon, {
            appliedText: coupon.applied ? 'Aplicado' : 'Não Aplicado',
            account: {
              nameText: coupon.account.name || 'Não Vinculada'
            }
          })
        ))
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
