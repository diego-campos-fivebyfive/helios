<template lang="pug">
  .modal(v-show='open')
    header
      slot(name='header')
    section
      slot(name='section')
    footer.footer
      Button.button(
        v-on:click.native='closeModal',
        icon='times-circle-o',
        type='default-bordered',
        label='Fechar',
        pos='single')
      slot.button(name='buttons')
</template>

<script>
  export default {
    props: ['open'],
    methods: {
      closeModal() {
        this.$emit('close')
      }
    }
  }
</script>

<style lang="scss" scoped>
  .modal {
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

  footer,
  header,
  section {
    background-color: $ui-white-regular;
    padding: $ui-space-y $ui-space-x/2;
  }

  section {
    background-color: #f8fafb;
    border-bottom: $ui-space-y/10 solid $ui-divider-color;
    border-top: $ui-space-y/10 solid $ui-divider-color;
  }

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

  .list {
    padding-left: $ui-space-x/2;

    li {
      padding: $ui-space-y/5 0;
    }
  }

  .footer {
    display: flex;
    justify-content: space-between;
  }

  .button {
    margin: 0 $ui-space-x/2;
  }
</style>
