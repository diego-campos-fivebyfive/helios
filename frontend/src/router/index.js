import Vue from 'vue'
import VueAxios from 'vue-axios'
import axios from 'axios'
import Router from 'vue-router'
import VueCookies from 'vue-cookies'

import Account from '@/components/modules/account'
// import Coupon from '@/components/modules/coupon'
import Metric from '@/components/modules/metric'
import NotFound from '@/components/modules/notfound'

axios.defaults.headers.post['Content-Type'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'
axios.defaults.headers.common['Accept-Language'] = 'pt_BR'
axios.defaults.withCredentials = true
axios.defaults.baseURL = process.env.API_URL

Vue.use(VueAxios, axios)
Vue.use(VueCookies)
Vue.use(Router)

export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/account',
      name: 'Contas',
      component: Account
    },
    // {
    //   path: '/coupon',
    //   name: 'Cupons',
    //   component: Coupon
    // },
    {
      path: '/metrics',
      name: 'Métricas',
      component: Metric
    },
    {
      path: '*',
      name: 'not-found',
      component: NotFound,
      meta: {
        sidebar: 'none',
        mainbar: 'none'
      }
    }
  ]
})
