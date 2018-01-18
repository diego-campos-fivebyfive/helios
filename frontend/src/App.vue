<template>
  <div class="main">
    <Init/>
  </div>
</template>

<script>
import Init from './components/Init'

export default {
  name: 'app',
  components: {
    Init
  }
}
</script>

<style scoped>
  $ui-space: 15px;

  .main {
    padding: 0px $ui-space;
  }
</style>

