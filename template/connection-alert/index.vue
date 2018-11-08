<template lang="pug">
  .connection-alert(v-if='!showMessage')
    img.connection-alert-img(
      src='~theme/assets/media/no-internet.png')
    .connection-alert-message
      | Sem conexão com a internet!
      .connection-alert-message-tip
        | Cheque sua conexão e tente novamente.
        p
          | Esta mensagem desaparecerá automaticamente
          | quando a conexão for restabelecida.
        .connection-alert-try-reconect
          | Tentando reconectar...
    Button.connection-alert-hide(
      v-if='onDeveloperMode()',
      :action='hideMessage',
      class='default-bordered')
        | Ocultar mensagem
</template>

<script>
  export default {
    data: () => ({
      showMessage: navigator.onLine
    }),
    mounted() {
      const changeStatus = () => this.showMessage = navigator.onLine
      window.addEventListener('online', changeStatus, false)
      window.addEventListener('offline', changeStatus , false)
    },
    methods: {
      hideMessage() {
        this.showMessage = true
      },
      onDeveloperMode() {
        return process.env.NODE_ENV === 'development'
      }
    }
  }
</script>

<style lang="scss" scoped>
  .connection-alert {
    align-items: center;
    animation: pulse 1s;
    background: $ui-white-regular;
    display: flex;
    flex-direction: column;
    height: 100%;
    line-height: normal;
    position: absolute;
    transition:
      border-color 150ms ease-in-out 0s,
      box-shadow 150ms ease-in-out 0s;
    width: 100%;
    z-index: 110;
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
    margin-top: $ui-space-y * 2;
  }

  .connection-alert-img {
    margin-top: $ui-space-y * 10;
  }

  .connection-alert-hide {
    animation: pulse 2s;
    display: block;
  }

  .connection-alert-try-reconect {
    color: $ui-orange-light;
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
