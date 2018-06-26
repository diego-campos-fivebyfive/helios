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

/* router.beforeEach((to, from, next) => {
  const path = {
    destiny: to.path,
    origin: from.path,
    notfound: '/not-found'
  }

  if (path.origin === path.notfound) {
    router.back()
    return
  }

  if (path.destiny === path.notfound) {
    next()
    return
  }

  if (to.path === '/terms') {
    next()
    return
  }

  axios.get('api/v1/application/menu')

    .then(({ data: menu }) => Object.values(menu)
      .some(({ link: itemLink }) =>
        (itemLink !== '/' && path.destiny.includes(itemLink))))

    .then(allowedRoute => {
      if (!allowedRoute) {
        next(path.notfound)
        return
      }

      next()
    })

  const uri = '/api/v1/terms/checker'
  axios.get(uri).catch(() => next('/terms'))

  next()
}) */

export { axios }
