<template lang="pug">
  .collection-notification(v-show='open')
    span.collection-notification-message
      Icon(name='check', scale='1.5')
      | {{ message }}
</template>

<script>
  export default {
    data: () => ({
      open: false,
      message: ''
    }),
    methods: {
      notify(message) {
        this.open = true
        this.message = message

        setTimeout(() => {
          this.open = false
        }, 5000)
      }
    }
  }
</script>

<style lang="scss">
  .collection-notification {
    left: 0;
    padding-top: $ui-space-y/2;
    position: fixed;
    right: 0;
    text-align: center;
    top: 0;
  }

  .collection-notification-message {
    background-color: $ui-blue-light;
    border-radius: $ui-corner;
    box-shadow: 0 0 2px rgba(0, 0, 0, 0.25);
    color: $ui-white-regular;
    display: inline-block;
    padding: $ui-space-y/1.5 $ui-space-x;

    svg {
      margin-right: $ui-space-x/2;
      vertical-align: middle;
    }
  }
</style>
