import Vuex from 'vuex'

import themeStore from 'theme/store'

const requireVueModules = require.context('@/', true, /data$/)

const modules = requireVueModules
  .keys()
  .reduce((acc, filename) => {
    const moduleName = filename.split('/')[1]
    const module = requireVueModules(filename)

    acc[moduleName] = module.default || module
    return acc
  }, themeStore.default || themeStore)

const strict = process.env.NODE_ENV !== 'production'

const install = Vue => {
  Vuex.install(Vue)
  Vue.prototype.$store = new Vuex.Store({ strict, modules })
}

export default {
  install
}
