const requireVueRoutes = require.context('@/', true, /routes.js$/)

export const vueRoutes = requireVueRoutes
  .keys()
  .map(requireVueRoutes)
  .reduce((acc, { routes }) => [...acc, ...routes], [])
