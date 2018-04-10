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

export default assignPayload
