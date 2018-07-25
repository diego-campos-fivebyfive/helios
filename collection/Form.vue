<template lang="pug">
  .collection-form
    Notification(ref='notification')
    Panel
      h1.title(slot='header')
        | {{ action.title }}
      form.form(slot='section')
        component(
          v-for='(field, name) in payload',
          v-if='field.component',
          :is='field.component',
          :key='name',
          :field='field',
          :style='getFieldSize(field.style.size)',
          v-on:validate='validate',
          v-on:disableFields='disableFields')
      component(
        slot='footer',
        v-on:done='done',
        :is='action.component',
        :payload='payload')
</template>

<script>
  import styles from 'theme/assets/style/main.scss'
  import { validate } from 'theme/validation/validate'
  import payload from 'theme/payload'

  export default {
    data: () => ({
      action: {
        layout: {
          columns: {}
        }
      },
      payload: {}
    }),
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

        const baseSize = parseInt(styles[`ui-size-${sizeType}`], 10)
        const spaces = parseInt(styles['ui-space-x'], 10) * 2

        return (baseSize - spaces) / this.action.layout.columns.total
      }
    },
    mounted() {
      this.setPayloadAction()
    },
    methods: {
      done(response) {
        const { notify } = this.$refs.notification

        response
          .then(message => {
            this.$emit('updateList')
            notify(message, 'primary-common')
          })
          .catch(message => {
            notify(message, 'danger-common')
          })
      },
      disableFields({ state, manager }) {
        this.payload.forEach(item => {
          if (
            item.disabled
            && item.disabled.manager === manager
          ) {
            this.$set(item.disabled, 'state', state)
          }
        })
      },
      getFieldSize([grow, shrink, cols]) {
        const base = this.getColumnsSize * cols
        return `flex: ${grow} ${shrink} ${base}px`
      },
      setPayloadAction() {
        const data = {}
        const action = 'create'

        const currentAction = this.actions[action]
        const defaultActionParams = this.actions.default || {}

        if (!currentAction.component) {
          throw new Error(`Error: ${action} action component is not defined`)
        }

        this.action = Object.assign(defaultActionParams, currentAction)
        this.payload = payload.init(this.schema, data, this.$set)
      },
      validate(field) {
        const { rejected, exception } = validate(field)

        this.$set(field, 'rejected', rejected)

        if (rejected) {
          const { notify } = this.$refs.notification
          notify(exception, 'danger-common')
        }
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
