import exceptions from 'theme/validation/locale/pt-br'
import patterns from 'theme/validation/patterns'

const validateRequired = field => {
  if (!field.value && field.required) {
    return {
      rejected: true,
      exception: `Campo ${field.label} requirido`
    }
  }

  return {
    rejected: false
  }
}

const validateType = field => {
  const pattern = patterns[field.type]
  const defaultException = exceptions[field.type]

  if (field.value) {
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

  return validateRequired(field)
}

export const validate = field => (
  (field.type && field.component.name === 'Check')
    ? validateType(field)
    : validateRequired(field)
)
