const extractPayload = payload => {
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

export default extractPayload
