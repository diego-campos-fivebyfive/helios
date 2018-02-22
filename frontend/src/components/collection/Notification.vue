<template lang="pug">
  .collection-notification(v-show='open')
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
    position: fixed;
  }
</style>
