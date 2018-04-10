<template lang="pug">
  .collection-form
    Notification(ref='notification')
    Modal(ref='modal')
      h1.title(slot='header')
        | {{ action.title }}
      form.form(slot='section')
        component(
          v-for='(field, name) in payload',
          v-if='field.component',
          :is='field.component',
          :key='name',
          :style='getFieldSize(field.style.size)',
          :label='field.label',
          :params='field',
          :updateField='updateField',
          :validateField='validateField')
      component(
        slot='buttons',
        :is='action.component')
</template>

<script>
  import styles from '@/theme/assets/style/main.scss'
  import payload from '@/theme/validation/payload'

  const { assignPayload, getPayload, isInvalidField } = payload

  export default {
    data: () => ({
      action: {
      },
      payload: {}
    }),
    props: [
      'modal'
    ],
    methods: {
      hide() {
        this.$refs.modal.hide()
      },
      show(action, data) {
        const currentAction = this.actions[action]
        const defaultActionParams = this.actions.default || {}

        if (!currentAction.component) {
          throw `Error: ${action} action component is not defined`
        }

        this.action = Object.assign(currentAction, defaultActionParams)
        this.payload = assignPayload(this.schema, data, this)
        this.$refs.modal.show()
      },
      notify(message, type) {
        this.$refs.notification.notify(message, type)
      },
      getFieldSize([grow, shrink, cols]) {
        const base = this.getColumnsSize * cols
        return `flex: ${grow} ${shrink} ${base}px`
      }
    },
    computed: {
      getColumnsSize() {
        const sizeTypes = {
          'extra-large': 'xl',
          'extra-small': 'xs',
          large: 'lg',
          medium: 'md',
          small: 'sm'
        }

        const sizeType = sizeTypes[this.action.layout.columns.size]

        const baseSize = parseInt(styles[`ui-size-${sizeType}`])
        const spaces = parseInt(styles['ui-space-x']) * 2

        return (baseSize - spaces) / this.action.layout.columns.total
      }
    }
  }
</script>

<style lang="scss" scoped>
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
