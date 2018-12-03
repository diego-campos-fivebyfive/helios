<template lang="pug">
  div(:class='elementClass')
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
    },
    computed: {
      elementClass() {
        return Math.random().toString(36).substr(2, 9)
      }
    }
  }
</script>

<style lang="scss" scoped>
  // Cover style
</style>
