<template lang="pug">
  .collection-modal-confirm(v-show='open')
    header.collection-modal-confirm-header
      slot(name='header')
    section.collection-modal-confirm-section
      slot(name='content')
    footer.collection-modal-confirm-footer
      Button(
        :action='hide',
        class='default-bordered',
        label='Fechar')
        Icon(name='times-circle-o')
      Button(
        :action='removeItem',
        class='danger-common',
        label='Confirmar')
        Icon(name='trash')
</template>

<script>
  export default {
    data: () => ({
      open: false,
      id: Number
    }),
    methods: {
      hide() {
        this.open = false
      },
      show(id) {
        this.id = id
        this.open = true
      },
      removeItem() {
        this.$emit('removeItem', this.id)
      }
    }
  }
</script>

<style lang="scss">
  .collection-modal-confirm {
    bottom: 0;
    color: $ui-text-main;
    left: 0;
    margin: auto;
    max-height: 50%;
    max-width: $ui-size-xs;
    position: fixed;
    right: 0;
    top: 0;
    z-index: 205;

    &:before {
      content: "";
      background-color: rgba(0, 0, 0, 0.25);
      height: 100%;
      left: 0;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: -1;
    }
  }

  .collection-modal-confirm-footer,
  .collection-modal-confirm-header,
  .collection-modal-confirm-section {
    background-color: $ui-white-regular;
    padding: $ui-space-y $ui-space-x/2;
    text-align: center;
  }

  .collection-modal-confirm-section {
    background-color: #f8fafb;
    border-bottom: $ui-space-y/10 solid $ui-divider-color;
    border-top: $ui-space-y/10 solid $ui-divider-color;
    color: $ui-text-main;

    .icon {
      opacity: 0.5;
    }

    .title {
      text-align: center;
      font-size: 1.75rem;
      font-weight: 600;
      line-height: 1.5;
    }

    .sub-title {
      font-size: 1rem;
      font-weight: 300;
      line-height: 1.5;
    }
  }

  .collection-modal-confirm-footer {
    display: flex;
    justify-content: space-between;
  }
</style>
