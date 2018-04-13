import exceptions from '@/theme/validation/locale/pt-br'
import patterns from '@/theme/validation/patterns'

const validate = field => {
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

export default validate
