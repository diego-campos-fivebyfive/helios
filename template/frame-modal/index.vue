<template lang="pug">
  transition(name='fade')
    .frame-modal(
      v-show='showFrameModal()',
      v-on:click='hideFrameModal')
      iframe.frame-modal-view(:src='path')
</template>

<script>
  export default {
    props: {
      handleTwigModal: {
        type: Object,
        required: true
      }
    },
    data: () => ({
      path: ''
    }),
    mounted() {
      window.renderFrameModal = this.renderFrameModal
      this.closeModalWhenBackButtonPressed()
    },
    methods: {
      closeModalWhenBackButtonPressed() {
        window.onpopstate = this.hideFrameModal
      },
      hideFrameModal() {
        this.path = ''
        this.handleTwigModal.toogle()
      },
      renderFrameModal(path) {
        const baseUri = process.env.API_URL
        const serializedPath = this.serializeBackSlash(path)

        this.path = `${baseUri}/${serializedPath}`
      },
      serializeBackSlash(str) {
        return str.replace(/^\/|\/$/g, '')
      },
      showFrameModal() {
        return Boolean(this.path)
      }
    }
  }
</script>

<style lang="scss">
  $view-space-x: $ui-space-x * 4;
  $view-space-y: $ui-space-y * 4;

  .frame-modal {
    background-color: rgba(0, 0, 0, 0.5);
    bottom: 0;
    height: 100%;
    left: 0;
    position: fixed;
    right: 0;
    top: 0;
    width: 100%;
    z-index: 400;
  }

  .frame-modal-view {
    background-color: $ui-white-regular;
    border: none;
    height: calc(100% - #{$view-space-y * 2});
    outline: none;
    margin: $view-space-y $view-space-x;
    width: calc(100% - #{$view-space-x * 2});
  }

  .fade-enter-active,
  .fade-leave-active {
    transition: all 300ms ease;
  }

  .fade-enter,
  .fade-leave-to {
    opacity: 0;
  }
</style>
