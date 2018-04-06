<template lang="pug">
  .collection-form
    Notification(ref='notification')
    Modal(ref='modal')
      h1.title(slot='header')
        | {{ getFormTitle }}
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
    },
    computed: {
      getFormTitle() {
        const { titles, current } = this.action
        return titles[current]
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
