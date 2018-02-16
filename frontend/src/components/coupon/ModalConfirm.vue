<template lang="pug">
  Modal(:open='modal.open', v-on:close='modal.open = false')
    h2.title(slot='header')
      | Confirma exclusão deste Cupom?
    Button(
      slot='buttons',
      icon='trash',
      type='danger-common',
      label='Confirmar',
      pos='single',
      v-on:click.native='removeCoupon(coupon.id)')
</template>

<script>
  export default {
    data: () => ({
      modal: {
        open: false
      },
      coupon: {}
    }),
    methods: {
      closeActionModal() {
        this.modal.open = false
      },
      showActionModal(coupon) {
        this.coupon = coupon
        this.modal.open = true
      },
      removeCoupon(id) {
        this.$emit('removeCoupon', id)
      }
    }
  }
</script>

<style lang="scss" scoped>
  .title {
    color: $ui-text-main;
    font-size: 2.25rem;
    font-weight: 600;
    text-align: center;
  }
</style>
