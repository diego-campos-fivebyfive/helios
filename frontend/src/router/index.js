import Vue from 'vue'
import VueAxios from 'vue-axios'
import VueRouter from 'vue-router'
import VueCookies from 'vue-cookies'

import { axios } from './axios'
import { routes } from './routes'

Vue.use(VueAxios, axios)
Vue.use(VueCookies)
Vue.use(VueRouter)

export const router = new VueRouter({
  mode: 'history',
  routes
})

router.beforeEach((to, from, next) => {
  if (to.path === '/terms') {
    next()
    return
  }

  const uri = '/api/v1/terms/checker'
  axios.get(uri).catch(() => next('/terms'))

  next()
})

export { axios }
