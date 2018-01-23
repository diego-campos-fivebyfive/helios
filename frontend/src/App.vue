<template lang="pug">
  .main
    img(src='@/assets/media/logo.png')
    router-view
    Init
</template>

<script>
  import Init from './components/Init'

  export default {
    name: 'App',
    components: {
      Init
    }
  }
</script>

<style lang="scss" scoped>
  $ui-space: 15px;

  .main {
    padding: 0 $ui-space;
  }
</style>
