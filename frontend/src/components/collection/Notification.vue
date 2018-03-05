<template lang="pug">
  .collection-notification(v-show='open')
    span.collection-notification-message
      Icon(name='check', scale='1.5')
      | {{ message }}
      slot
      .collection-notification-status
</template>

<script>
  export default {
    data: () => ({
      open: true,
      message: 'Teste'
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

  @keyframes lifetime {
    from {
      width: 100%;
    }

    to {
      width: 0;
    }
  }

  .collection-notification-status {
    animation: lifetime 5s 1;
    height: $ui-space-y/3;
    background-color: $ui-blue-dark;
    border-radius: $ui-corner;
  }
</style>
