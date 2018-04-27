<template lang="pug">
  a.notify-message(href='/messenger')
    Icon.notify-message-icon(name='envelope')
    label.notify-message-label 01
</template>

<script>
  export default {
  }
</script>

<style lang="scss" scoped>
  .notify-message {
    margin-right: 0.75rem;

    .notify-message-icon {
      display: inline-block;
      z-index: 1;
    }

    .notify-message-label {
      background-color: $ui-orange-light;
      border-radius: 0.25rem;
      color: $ui-white-regular;
      font-size: 0.8rem;
      font-weight: 600;
      padding: 0.25rem;
      position: absolute;
      right: 7.75rem;
      top: 0.8rem;
      z-index: 2;
    }
  }

  a {
    color: $ui-gray-regular;
  }
</style>
