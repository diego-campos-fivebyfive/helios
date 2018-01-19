<template>
  <div class="main">
    <router-view/>
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
