<template lang="pug">
  .collection-form
    Notification(ref='notification')
    Modal(ref='modal')
      h1.title(slot='header')
        | {{ action.title }}
      form.form(slot='section')
        component(
          v-for='(field, name) in payload',
          :key='name',
          :is='field.component',
          :label='field.label',
          :params='field',
          :updateField='updateField',
          :validateField='validateField')
      component(
        slot='buttons',
        :is='action.component')
</template>

<script>
  import payload from '@/theme/validation/payload'

  const { assignPayload, getPayload, isInvalidField } = payload

  export default {
    data: () => ({
      action: {}
    }),
    props: [
      'modal'
    ],
    methods: {
      hide() {
        this.$refs.modal.hide()
      },
      show(action, coupon) {
        this.action = this.actions[action]
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
