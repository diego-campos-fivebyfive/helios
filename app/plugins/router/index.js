import VueRouter from 'vue-router'

import { routes } from './routes'
import { checkAccess } from './access'

const mode = process.env.PLATFORM === 'web' ? 'history' : 'hash'

const router = new VueRouter({
  mode,
  routes
})

router.beforeEach(checkAccess)

const install = Vue => {
  VueRouter.install(Vue)
}

export default {
  install,
  router
}
