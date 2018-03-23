<template lang="pug">
  .collection-modal-form
    Notification(ref='notification')
    Modal(ref='modal')
      slot(name='header', slot='header')
      slot(name='section', slot='section')
      slot(name='buttons', slot='buttons')
</template>

<script>
  export default {
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
  .collection-modal-form {
    .form {
      align-content: flex-start;
      align-items: flex-start;
      display: flex;
      flex-flow: row wrap;
      justify-content: flex-start;
      width: 100%;

      label {
        float: left;
        font-weight: 600;
        padding: $ui-space-y/2 $ui-space-x/2;
      }

      select {
        padding: 0 $ui-space-x/3;
      }

      input {
        padding: 0 $ui-space-x/2;

        @include placeholder-color($ui-gray-regular);
      }

      input,
      select {
        background-color: $ui-white-regular;
        border: 1px solid $ui-gray-light;
        border-radius: 1px;
        color: $ui-gray-dark;
        display: block;
        height: $ui-action-y;
        margin-top: $ui-space-y/2;
        width: 100%;
        transition:
          border-color 150ms ease-in-out 0s,
          box-shadow 150ms ease-in-out 0s;

        &:focus {
          border-color: $ui-blue-light;
        }
      }
    }
  }
</style>
