import Vue from 'vue'
import VueAxios from 'vue-axios'
import axios from 'axios'
import Router from 'vue-router'

import Account from '@/components/Account'
import Metric from '@/components/metric'
import NotFound from '@/components/NotFound'

axios.defaults.headers.post['Content-Type'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'
axios.defaults.headers.common['Accept-Language'] = 'pt_BR'
axios.defaults.baseURL = process.env.API_URL

Vue.use(VueAxios, axios)
Vue.use(Router)

export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/account',
      name: 'account',
      component: Account
    },
    {
      path: '/metric',
      name: 'metric',
      component: Metric
    },
    {
      path: '*',
      name: 'not-found',
      component: NotFound
    }
  ]
})
