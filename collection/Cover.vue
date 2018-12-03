<template lang="pug">
  .wrapper
</template>

<script>
  import { insertBackground } from './cover'

  export default {
    props: {
      width: {
        type: Number,
        required: false,
        default: window.innerWidth
      },
      height: {
        type: Number,
        required: false,
        default: window.innerHeight
      },
      speed: {
        type: Number,
        required: false,
        default: 12000
      },
      scale: {
        type: Number,
        required: false,
        default: 15
      }
    },
    mounted() {
      const { width, height, speed, scale } = this

      insertBackground({
        element: this.$el,
        width,
        height,
        speed,
        scale
      })
    }
  }
</script>

<style lang="scss" scoped>
  // Cover style
</style>
