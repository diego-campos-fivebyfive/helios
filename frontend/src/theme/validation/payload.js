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
  const updateTree = (obj, [pos, ...path], value) => {
    if (!pos) {
      return obj = value
    }

    return updateTree(obj[pos], path, value)
  }

  return payload.reduce((acc, { path, value }) => (
    updateTree(acc, path, value)
  ), {})
}

const assignPayload = (schema, data = {}, self) => {
  const assign = (schemaObj, dataObj = {}, path = [], fields = []) =>
    Object
      .entries(schemaObj)
      .reduce((acc, [key, val]) => {
        const newPath = path.push(key)
        if (
          Object.keys(val).length > 0
          && !Object.prototype.hasOwnProperty.call(val, 'component')
        ) {
          assign(val.value, dataObj[key], newPath, fields)
          return acc
        }

        const field = val || {}
        self.$set(field, 'value', dataObj[key] || null)
        self.$set(field, 'path', newPath)

        if (Object.prototype.hasOwnProperty.call(val, 'type')) {
          self.$set(field, 'rejected', false)
        }

        return acc.concat(field)
      }, fields || [])

  return assign(schema, data)
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
