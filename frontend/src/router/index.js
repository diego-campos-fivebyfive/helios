import Vue from 'vue'
import Router from 'vue-router'
import Init from '../components/Init'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/init',
      name: 'Init',
      component: Init
    }
  ]
})
