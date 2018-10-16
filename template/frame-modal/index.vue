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
    border-bottom: ($ui-corner / 3) solid $ui-gray-lighter;
    border-radius: 4px;
    border: ($ui-corner / 3) solid $ui-gray-regular;
    height: calc(100% - #{$view-space-y * 2});
    margin: $view-space-y $view-space-x;
    outline: none;
    width: calc(100% - #{$view-space-x * 2});
  }

  .frame-modal-view-content {
    border: none;
    height: 90%;
    padding: $ui-corner;
    width: 100%;
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

  @media screen and (max-width: $small-device) {
    .frame-modal {
      z-index: 0;
    }

    .frame-modal-view {
      height: 150vw;
      margin-top: $ui-mainbar-mobile-y + $ui-corner;
      margin: $ui-corner * 2;
      width: calc(100vw - #{$ui-space-x / 2});
    }
  }
</style>
