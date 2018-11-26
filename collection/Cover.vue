<template lang="pug">
  div(:class='elementClass')
</template>

<script>
  import { insertBackground } from './cover'

  export default {
    props: {
      elementClass: {
        type: String,
        required: true
      },
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
      const { width, height, speed, scale, elementClass } = this

      insertBackground({
        elementClass: `.${elementClass}`,
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
