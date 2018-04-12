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
  (!emptyObject(obj) && !typeLeaf(obj))

const deepTree = ({
  schema,
  data = {},
  payload = [],
  path = []
}, setAttr) => (
  Object
    .entries(schema)
    .reduce((acc, [key, value = {}]) => {
      if (typeGroup(value)) {
        return deepTree({
          path: getNextPath(path, key),
          data: data[key],
          schema: value,
          payload
        }, setAttr)
      }

      const item = Object.assign({}, value)

      setAttr(item, 'value', data[key] || null)
      setAttr(item, 'path', path)
      setAttr(item, 'name', key)

      acc.push(item)
      return acc
    }, payload)
)

const init = (schema, data, setAttr) =>
  deepTree({ schema, data }, setAttr)

export default init
