import exceptions from '@/theme/validation/locale/pt-br'
import patterns from '@/theme/validation/patterns'

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

export default {
  isInvalidField,
  isValidPayload
}
