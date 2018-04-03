import Vue from 'vue'
import Router from 'vue-router'
import VueCookies from 'vue-cookies'
import VueAxios from 'vue-axios'
import axios from './axios'

import Account from '@/pages/account'
// import Coupon from '@/pages/coupon'
import Metric from '@/pages/metric'
import NotFound from '@/pages/notfound'

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
