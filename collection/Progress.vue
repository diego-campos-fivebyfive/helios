<template lang="pug">
  .collection-progress
    .collection-progress-bar
      .collection-progress-status(:style='{ width: `${percent}%` }')
    slot
</template>

<script>
  export default {
    props: {
      percent: {
        type: Number,
        required: true
      }
    }
  }
</script>

<style lang="scss">
  .collection-progress {
    .caption {
      display: inline-block;
      margin-top: $ui-space-y/3;
      margin-left: $ui-space-x/3;
    }
  }

  .collection-progress-bar {
    width: 100%;
    background-color: $ui-gray-lighter;
    border-radius: $ui-corner;
    overflow: hidden;
    height: $ui-space-y/1.8;
    margin: $ui-space-y/3 0;
    margin-right: $ui-space-x/5;
    position: relative;
  }

  .collection-progress-status {
    background-color: $ui-green-regular;
    position: absolute;
    height: 100%;
    right: 0;
    top: 0;
  }
</style>
