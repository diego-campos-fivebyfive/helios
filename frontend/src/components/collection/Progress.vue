<template lang="pug">
  div.bar
    div.progress(:style="{ width: percent + '%' }")
</template>

<script>
  export default {
    props: ['percent']
  }
</script>

<style lang="scss" scoped>
  .bar {
    width: 100%;
    background-color: $ui-gray-light;
    border-radius: $ui-corner;
    overflow: hidden;
    height: $ui-space-y/2;
    position: relative;
    margin: $ui-space-y/3 $ui-space-x/5;
  }

  .progress {
    background-color: $ui-green-regular;
    position: absolute;
    height: 100%;
    right: 0;
    top: 0;
  }
</style>
