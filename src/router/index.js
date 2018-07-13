import Vue from 'vue'
import VueAxios from 'vue-axios'
import VueRouter from 'vue-router'
import VueCookies from 'vue-cookies'

import { axios } from './axios'
import { routes } from './routes'

Vue.use(VueAxios, axios)
Vue.use(VueCookies)
Vue.use(VueRouter)

export const router = user => {
  const Router = new VueRouter({
    mode: 'history',
    routes
  })

  Router.beforeEach((to, from, next) => {
    if (user.sices) {
      next()
      return
    }

    if (to.path === '/terms') {
      next()
      return
    }

    axios.get('/api/v1/terms/checker')
      .then(() => {
        next()
      })
      .catch(() => {
        next('/terms')
      })
  })

  return Router
}

export { axios }
