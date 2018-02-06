<template lang="pug">
  div
    Table(type='striped')
      tr(slot='head')
        th Nome
        th Valor
        th Conta
        th Status
        th Ações
      tr.rows(slot='rows', v-for='coupon in coupons')
        td {{ coupon.name }}
        td {{ coupon.amount }}
        td.center {{ coupon.account }}
        td.center {{ coupon.status }}
        td.center
          Button(
            type='primary-common',
            icon='pencil',
            link='#linkB',
            label='Editar',
            pos='first')
          Button(
            type='danger-common',
            icon='trash',
            link='#linkB',
            label='Deletar',
            pos='last')
</template>

<script>
  export default {
    props: [
      'coupons'
    ],
    mounted() {
    }
  }
</script>

<style lang="scss" scoped>
  .rows {
    cursor: pointer;
  }

  .center {
    text-align: center;
  }
</style>
