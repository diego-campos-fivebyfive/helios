const emptyObject = obj =>
  Object.keys(obj).length === 0

const isObject = val =>
  val === Object(val)

const getNextPath = (path, key) => {
  const pathCopy = path.slice()
  pathCopy.push(key)
  return pathCopy
}

const typeLeaf = obj =>
  Object
    .values(obj)
    .reduce((acc, value) => (
      acc || !isObject(value)
    ), false)

const typeGroup = obj =>
  !emptyObject(obj) && !typeLeaf(obj)

const deepTree = (obj, payload = [], path = [], data = {}) =>
  Object
    .entries(obj)
    .reduce((acc, [key, value = {}]) => {

      if (typeGroup(value)) {
        return deepTree(value, payload, getNextPath(path, key), data[key])
      }

      const item = Object.assign({}, value)

      item.name = key
      item.path = path
      item.value = data[key] || null

      acc.push(item)
      return acc

    }, payload)

const init = (schema, data) =>
  deepTree(schema, [], [], data)

export default init
