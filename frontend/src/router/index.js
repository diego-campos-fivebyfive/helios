import Vue from 'vue'
import Router from 'vue-router'
import Account from '@/components/Account'
import Metric from '@/components/metric'

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
    }
  ]
})
