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
  if(to.path !== '/terms') {
    const uri = '/api/v1/terms/checker'
    axios.get(uri)
      .then(() => next())
      .catch(() => next('/terms'))
  } else {
    next()
  }
})

export { axios }
