<template lang="pug">
  .collection-modal(v-show='open')
    header.collection-modal-header
      slot(name='header')
    section.collection-modal-section
      slot(name='section')
    footer.collection-modal-footer
      Button(
        v-on:click.native='hide',
        icon='times-circle-o',
        type='default-bordered',
        label='Fechar',
        pos='single')
      slot(name='buttons')
</template>

<script>
  export default {
    data: () => ({
      open: false
    }),
    methods: {
      show() {
        this.open = true
      },
      hide() {
        this.open = false
      }
    }
  }
</script>

<style lang="scss">
  .collection-modal {
    bottom: 0;
    color: $ui-text-main;
    left: 0;
    margin: auto;
    max-height: 50%;
    max-width: 50%;
    position: fixed;
    right: 0;
    top: 0;
    z-index: 1;

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

  .collection-modal-footer,
  .collection-modal-header,
  .collection-modal-section {
    background-color: $ui-white-regular;
    padding: $ui-space-y $ui-space-x/2;
  }

  .collection-modal-header {
    .title {
      font-size: 1.75rem;
      font-weight: 300;

      .sub {
        display: block;
        font-size: 1rem;
        font-style: italic;
        font-weight: 400;
        padding-top: $ui-space-y/1.5;
      }
    }
  }

  .collection-modal-section {
    background-color: #f8fafb;
    border-bottom: $ui-space-y/10 solid $ui-divider-color;
    border-top: $ui-space-y/10 solid $ui-divider-color;

    .list {
      padding-left: $ui-space-x/2;

      li {
        padding: $ui-space-y/5 0;
      }
    }
  }

  .collection-modal-footer {
    display: flex;
    justify-content: space-between;
  }
</style>
