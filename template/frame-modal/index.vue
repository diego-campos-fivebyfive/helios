<template lang="pug">
  transition(name='fade')
    .frame-modal(
      v-show='showFrameModal()',
      v-on:click='hideFrameModal')
      .frame-modal-view
        iframe.frame-modal-view-content(:src='path')
        .frame-modal-view-footer
          Button.frame-modal-view-close(
            :action='hideFrameModal',
            class='default-bordered')
              | Fechar
</template>

<script>
  export default {
    props: {
      twigModalState: {
        type: Boolean,
        required:true
      }
    },
    data: () => ({
      path: ''
    }),
    mounted() {
      window.renderFrameModal = this.renderFrameModal
      window.onpopstate = this.hideFrameModal
    },
    methods: {
      hideFrameModal() {
        this.path = ''
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
    },
    watch: {
        twigModalState: {
          handler: 'hideFrameModal'
        }
      }
  }
</script>

<style lang="scss">
  $small-device: 768px;
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
    @media screen and (max-width: $small-device) {
      z-index: 0;
    }
  }

  .frame-modal-view {
    background-color: $ui-white-regular;
    height: calc(100% - #{$view-space-y * 2});
    outline: none;
    margin: $view-space-y $view-space-x;
    width: calc(100% - #{$view-space-x * 2});
    border-bottom: ($ui-corner / 3) solid $ui-gray-lighter;
    border: ($ui-corner / 3) solid $ui-gray-regular;
    border-radius: 4px;

    @media screen and (max-width: $small-device) {
      margin: $ui-corner * 2;
      margin-top: $ui-mainbar-mobile-y + $ui-corner;
      height: 150vw;
      width: calc(100vw - #{$ui-space-x / 2});
    }
  }

  .frame-modal-view-content {
    width: 100%;
    height: 90%;
    border: none;
    padding: $ui-corner;
  }

  .frame-modal-view-footer {
    width: 100%;
  }

  .frame-modal-view-close {
    float: right;
    margin: $ui-space-x / 2;
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
