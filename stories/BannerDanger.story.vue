<template lang="pug">
  Banner(
    icon='arrow-down'
    message='Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et'
    title='Danger banner!'
    type='danger'
  )
</template>

<script>
  import Banner from '../collection/Banner'

  export default {
    name: 'BannerDanger',
    components: {
      Banner
    }
  }
</script>

<style lang="scss" scoped>
/* BannerDanger Style */
</style>
