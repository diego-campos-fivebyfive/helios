<template lang="pug">
  .collection-form
    Notification(ref='notification')
    Modal(ref='modal')
      h1.title(slot='header')
        | {{ action.title }}
      form.form(slot='section')
        component(
          v-on:validate='validate',
          v-for='(field, name) in payload',
          v-if='field.component',
          :is='field.component',
          :key='name',
          :field='field',
          :style='getFieldSize(field.style.size)')
      component(
        slot='buttons',
        :is='action.component',
        :payload='payload')
</template>

<script>
  import styles from '@/theme/assets/style/main.scss'
  import validate from '@/theme/validation/validate'
  import payload from '@/theme/payload'

  export default {
    data: () => ({
      action: {
        layout: {
          columns: {}
        }
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

        this.action = Object.assign(defaultActionParams, currentAction)
        this.payload = payload.assign(this.schema, data, this)
        this.$refs.modal.show()
      },
      notify(message, type) {
        this.$refs.notification.notify(message, type)
      },
      validate(field) {
        const { rejected, exception } = validate(field)

        this.$set(field, 'rejected', rejected)

        if (rejected) {
          this.notify(exception, 'danger-common')
        }
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
          'large': 'lg',
          'medium': 'md',
          'small': 'sm'
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
