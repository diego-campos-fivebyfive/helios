<template lang="pug">
  div
    .bar
      .progress(:style='{ width: `${percent}%` }')
    slot
</template>

<script>
  export default {
    props: ['percent']
  }
</script>

<style lang="scss" scoped>
  .bar {
    width: 100%;
    background-color: $ui-gray-lighter;
    border-radius: $ui-corner;
    overflow: hidden;
    height: $ui-space-y/1.8;
    margin: $ui-space-y/3 0;
    margin-right: $ui-space-x/5;
    position: relative;
  }

  .progress {
    background-color: $ui-green-regular;
    position: absolute;
    height: 100%;
    right: 0;
    top: 0;
  }

  .caption {
    display: inline-block;
    margin-top: $ui-space-y/3;
    margin-left: $ui-space-x/3;
  }
</style>
