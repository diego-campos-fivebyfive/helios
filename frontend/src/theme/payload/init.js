const assignPayload = (schema, data = {}, setObjectAttrs) => {
  const assign = (schemaObj, dataObj = {}, path = [], fields = []) =>
    Object
      .entries(schemaObj)
      .reduce((acc, [key, val]) => {
        const newPath = path.slice()
        newPath.push(key)

        if (
          Object.keys(val).length > 0
          && !Object.prototype.hasOwnProperty.call(val, 'component')
        ) {
          assign(val.value, dataObj[key], newPath, fields)
          return acc
        }

        const field = val || {}

        const fieldAttrs = {
          name: key,
          path: newPath,
          value: dataObj[key] || null
        }

        if (Object.prototype.hasOwnProperty.call(val, 'type')) {
          fieldAttrs.rejected = false
        }

        setObjectAttrs(field, fieldAttrs)

        return acc.concat(field)
      }, fields || [])

  return assign(schema, data)
}

export default assignPayload
