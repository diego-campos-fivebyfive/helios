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

export { axios }
