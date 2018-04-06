import exceptions from '@/theme/locale/pt-br'
import patterns from '@/theme/validation/pattern'

const isInvalidField = field => {
  const pattern = patterns[field.type]
  const defaultException = exceptions[field.type]

  if (pattern.test(field.value)) {
    return {
      rejected: false
    }
  }

  return {
    rejected: true,
    exception: field.exception || defaultException
  }
}

const isValidPayload = payload => {
  /* eslint-disable no-use-before-define, no-restricted-syntax */
  const isResolved = (obj, key) => {
    const val = obj[key]

    if (val === Object(val)) {
      return isValid(val)
    }

    return (key === 'rejected' && val)
      ? !isInvalidField(obj)
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
}

const formatPayload = payload => {
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
}

const assignPayload = (payload, dataPayload = {}) => {
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
}

const getPayload = payload => {
  if (!isValidPayload(payload)) {
    return false
  }

  return formatPayload(payload)
}

export default {
  assignPayload,
  getPayload,
  isInvalidField
}
