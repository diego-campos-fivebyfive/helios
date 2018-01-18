<template>
  <div class="main">
    <Init/>
  </div>
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
