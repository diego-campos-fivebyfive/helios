<template lang="pug">
  .connection-alert(v-if='!connected')
    img.connection-alert-img(
      src='~theme/assets/media/no-internet.png')
    .connection-alert-message
      | No internet connection!
      .connection-alert-message-tip
        | Check your connection or try again
</template>

<script>
  export default {
    data: () => ({
      connected: navigator.onLine
    }),
    mounted() {
      console.log(this.connected)
      const changeStatus = () => {
        console.log('changed ', this.connected)
        this.connected = !this.connected
      }
      window.document.addEventListener('online', changeStatus, false)
      window.document.addEventListener('offline', changeStatus , false)
    }
  }
</script>

<style lang="scss" scoped>
  .connection-alert {
    background: white;
    position: absolute;
    width: 100%;
    height: 100%;
    animation: pulse 2s;
    transition:
      border-color 150ms ease-in-out 0s,
      box-shadow 150ms ease-in-out 0s;
  }

  .connection-alert-message {
    font-size: 25px;
    text-align: center;
    color: $ui-blue-light;
    font-weight: 600;
  }

  .connection-alert-message-tip {
    color: $ui-gray-regular;
    font-size: 15px;
  }

  .connection-alert-img {
    display: block;
    margin: 40% auto 10%;
  }

  @keyframes pulse {
    0% {
      opacity: 0;
    }
    100% {
      opacity: 1;
    }
  }
</style>
