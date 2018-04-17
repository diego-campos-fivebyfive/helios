import exceptions from '@/theme/validation/locale/pt-br'
import patterns from '@/theme/validation/patterns'

const validate = field => {
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

  if (field.required) {
    return {
      rejected: true,
      exception: `Campo ${field.label} requirido`
    }
  }

  return {
    rejected: false
  }
}

export default validate
