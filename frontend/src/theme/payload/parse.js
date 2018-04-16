const incrementTree = (
  tree = {},
  { name, value, path: [attr, ...path] }
) => {
  const node = {}

  if (attr) {
    const nextLevelNode = {
      name,
      value,
      path
    }

    node[attr] = incrementTree(tree[attr], nextLevelNode)
    return Object.assign(tree, node)
  }

  node[name] = value
  return Object.assign(tree, node)
}

const parse = payload =>
  payload.reduce(incrementTree, {})

export default parse
