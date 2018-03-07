<template lang="pug">
  div
    Notification(ref='notification')
    ModalConfirm(
      ref='modalConfirm',
      v-on:removeCoupon='removeCoupon')
    Table.table(type='stripped')
      tr(slot='head')
        th Nome
        th Código
        th Conta
        th Status
        th Valor
        th Ações
      tr.rows(slot='rows', v-for='coupon in coupons')
        td {{ coupon.name }}
        td {{ coupon.code }}
        td {{ coupon.account.name || 'Não Vinculada' }}
        td {{ coupon.applied ? 'Aplicado' : 'Não Aplicado' }}
        td {{ coupon.amount }}
        td
          Button(
            v-if='!coupon.applied',
            type='primary-common',
            icon='pencil',
            pos='first',
            v-on:click.native='showModalForm(coupon)')
          Button(
            v-if='!coupon.applied',
            type='danger-common',
            icon='trash',
            pos='last',
            v-on:click.native='showModalConfirm(coupon)')
</template>

<script>
  import ModalConfirm from './ModalConfirm'

  export default {
    components: {
      ModalConfirm
    },
    props: [
      'coupons'
    ],
    methods: {
      showModalForm(coupon) {
        this.$emit('showModalForm', 'edit', coupon)
      },
      showModalConfirm(coupon) {
        this.$refs.modalConfirm.showActionModal(coupon)
      },
      removeCoupon(id) {
        this.$refs.modalConfirm.closeActionModal()

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

  .table {
    td,
    th {
      &:nth-child(1) {
        text-align: left;
        width: 25%;
      }

      &:nth-child(2) {
        text-align: center;
      }

      &:nth-child(3) {
        text-align: center;
        width: 25%;
      }

      &:nth-child(4) {
        text-align: center;
      }

      &:nth-child(5) {
        text-align: right;
      }

      &:nth-child(6) {
        text-align: right;
      }
    }
  }
</style>
