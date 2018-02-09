<template lang="pug">
  div
    Table.table(type='striped')
      tr(slot='head')
        th Nome
        th Conta
        th Status
        th Valor
        th Ações
      tr.rows(slot='rows', v-for='coupon in coupons')
        td {{ coupon.name }}
        td {{ coupon.account }}
        td {{ coupon.applied ? 'Aplicado' : 'Não Aplicado' }}
        td {{ coupon.amount }}
        td
          Button(
            type='primary-common',
            icon='pencil',
            pos='first')
          Button(
            type='danger-common',
            icon='trash',
            pos='last',
            v-on:click.native='remove(coupon.id)')
</template>

<script>
  export default {
    props: [
      'coupons'
    ],
    methods: {
      remove(id) {
        this.axios.delete(`/api/v1/coupon/${id}`)
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
        width: 25%;
      }

      &:nth-child(3) {
        text-align: center;
      }

      &:nth-child(4) {
        text-align: right;
      }

      &:nth-child(5) {
        text-align: right;
      }
    }
  }
</style>
