<template lang="pug">
  .connection-alert(v-if='!connected')
    img.connection-alert-img(
      src='~theme/assets/media/no-internet.png')
    .connection-alert-message
      | Sem conexão com a internet!
      .connection-alert-message-tip
        | Cheque sua conexão e tente novamente.
        p
          | Esta mensagem desaparecerá quando a conexão for restabelecida.
</template>

<script>
  export default {
    data: () => ({
      connected: navigator.onLine
    }),
    mounted() {
      const changeStatus = () => {
        this.connected = !this.connected
      }
      window.document.addEventListener('online', changeStatus, false)
      window.document.addEventListener('offline', changeStatus , false)
    }
  }
</script>

<style lang="scss" scoped>
  .connection-alert {
    animation: pulse 2s;
    background: $ui-white-regular;
    height: 100%;
    position: absolute;
    transition:
      border-color 150ms ease-in-out 0s,
      box-shadow 150ms ease-in-out 0s;
    width: 100%;
  }

  .connection-alert-message {
    color: $ui-blue-light;
    font-size: 2em;
    font-weight: 600;
    margin: $ui-space-y;
    text-align: center;
  }

  .connection-alert-message-tip {
    color: $ui-gray-regular;
    font-size: 0.5em;
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
