<template lang="pug">
  .collection-form
    Notification(ref='notification')
    Modal(v-if='modal', ref='modal')
      slot(name='header', slot='header')
      slot(name='section', slot='section')
      slot(name='buttons', slot='buttons')
    div(v-else)
      slot(name='header', slot='header')
      slot(name='section', slot='section')
      slot(name='buttons', slot='buttons')
</template>

<script>
  export default {
    props: [
      'modal'
    ],
    methods: {
      hide() {
        this.$refs.modal.hide()
      },
      show() {
        this.$refs.modal.show()
      },
      notify(message, type) {
        this.$refs.notification.notify(message, type)
      }
    }
  }
</script>

<style lang="scss">
  .collection-form {
    form {
      align-content: flex-start;
      align-items: flex-start;
      display: flex;
      flex-flow: row wrap;
      justify-content: flex-start;
      width: 100%;
    }
  }
</style>
