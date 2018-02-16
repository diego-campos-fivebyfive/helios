<template lang="pug">
  Modal.modal(:open='modal.open', v-on:close='modal.open = false')
    div(slot='section')
      Icon.icon(name='question-circle-o', scale='4')
      h2.title
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
  .modal {
    max-width: $ui-size-xs;
    text-align: center;
  }

  .icon {
    margin-bottom: $ui-space-y/3;
    opacity: 0.5;
  }

  .title {
    color: $ui-text-main;
    font-size: 1.75rem;
    font-weight: 600;
    line-height: 1.5;
  }
</style>
