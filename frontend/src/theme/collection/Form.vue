<template lang="pug">
  .collection-modal-form
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
  import exceptions from '@/locale/pt-br'
  import patterns from '@/validation/pattern'

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
      },
      isInvalidField(field) {
        const pattern = patterns[field.type]

        const defaultException = exceptions[field.type]

        if (pattern.test(field.value)) {
          return false
        }

        this.notify(field.exception || defaultException, 'danger-common')
        return true
      },
      getPayloadField(vm, path) {
        return path
          .split('.')
          .reduce((obj, key) => obj[key], vm)
      },
      isValidPayload(payload) {
        /* eslint-disable no-use-before-define, no-restricted-syntax */
        const isResolved = (obj, key) => {
          const val = obj[key]

          if (val === Object(val)) {
            return isValid(val)
          }

          return (key === 'rejected' && val)
            ? !this.isInvalidField(obj)
            : true
        }

        const isValid = obj => {
          for (const key in obj) {
            if (!isResolved(obj, key)) return false
          }

          return true
        }

        return isValid(payload)
        /* eslint-enable no-use-before-define, no-restricted-syntax */
      },
      formatPayload(payload) {
        const format = obj =>
          Object
            .entries(obj)
            .reduce((acc, [key, val]) => {
              acc[key] = Object.prototype.hasOwnProperty.call(val, 'value')
                ? val.value
                : format(val)
              return acc
            }, {})

        return format(payload)
      },
      assignPayload(payload, dataPayload = {}) {
        const assign = (base, data = {}) =>
          Object
            .entries(base)
            .reduce((acc, [key, val]) => {
              if (
                Object.keys(val).length > 0
                && !Object.prototype.hasOwnProperty.call(val, 'value')
                && !Object.prototype.hasOwnProperty.call(val, 'type')
              ) {
                acc[key] = assign(val, data[key])
                return acc
              }

              acc[key] = val || {}
              this.$set(acc[key], 'value', data[key] || null)

              if (Object.prototype.hasOwnProperty.call(val, 'type')) {
                this.$set(acc[key], 'rejected', false)
              }

              return acc
            }, {})

        return assign(payload, dataPayload)
      },
      getPayload(payload) {
        if (!this.isValidPayload(payload)) {
          return false
        }

        return this.formatPayload(payload)
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
    }
  }
</style>
