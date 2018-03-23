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
      },
      validateField(field) {
        const patterns = {
          money: /^(\d{1,3}(\.\d{3})*|\d+)(\,\d{2})?$/
        }

        const pattern = patterns[field.type]

        const exceptions = {
          money: 'Formato de moeda em Real invalido'
        }

        const defaultException = exceptions[field.type]

        if (pattern.test(field.value)) {
          /* eslint-disable no-param-reassign */
          field.resolved = true
          /* eslint-enable no-param-reassign */
          return true
        }

        this.notify(field.exception || defaultException)
        /* eslint-disable no-param-reassign */
        field.resolved = false
        /* eslint-enable no-param-reassign */
        return false
      },
      isValidPayload(payload) {
        /* eslint-disable no-use-before-define, no-restricted-syntax */
        const isResolved = (obj, key) => {
          const val = obj[key]

          if (val === Object(val)) {
            return isValid(val)
          }

          return (key === 'resolved' && !val)
            ? this.validateField(obj)
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
              if (val === Object(val)) {
                acc[key] = assign(val, data[key] || '')
                return acc
              }

              if (key === 'default') {
                acc.value = data || val
              }

              acc[key] = (key === 'resolved') ? false : val
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
