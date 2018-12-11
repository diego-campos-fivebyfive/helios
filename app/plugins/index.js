import components from './component'
import filters from './filters'
import locale from './locale'
import modules from './modules'
import moment from './moment'
import router from './router'
import sockets from './sockets'

const plugins = {
  components,
  filters,
  locale,
  modules,
  moment,
  router,
  sockets
}

const install = (Vue, VueSet) => {
  Object
    .values(plugins)
    .forEach(plugin => {
      plugin.install(Vue)
    })

  VueSet({
    router: router.router
  })
}

export default {
  install
}
