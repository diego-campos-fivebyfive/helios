<template lang="pug">
  .frame-modal
    iframe.frame-modal-view(
      v-if='showFrameModal()',
      :src='path')
</template>

<script>
  export default {
    data: () => ({
      path: ''
    }),
    mounted() {
      window.renderFrameModal = this.renderFrameModal
    },
    methods: {
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
  .frame-modal-view {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    z-index: 400;
  }
</style>
