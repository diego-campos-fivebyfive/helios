<template lang="pug">
  Banner(
    icon='arrow-down'
    message='Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et'
    title='Info banner!'
    type='info'
  )
</template>

<script>
  import Banner from '../collection/Banner'

  export default {
    name: 'BannerInfo',
    components: {
      Banner
    }
  }
</script>

<style lang="scss" scoped>
/* BannerDanger Style */
</style>
