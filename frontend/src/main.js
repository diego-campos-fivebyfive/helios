import Icon from 'vue-awesome/components/Icon'
import 'vue-awesome/icons'

import Button from '@/components/collection/Button'
import Panel from '@/components/collection/Panel'
import Progress from '@/components/collection/Progress'
import Table from '@/components/collection/Table'

import Vue from 'vue'
import App from './App'
import router from './router'

Vue.component('Icon', Icon)
Vue.component('Button', Button)
Vue.component('Panel', Panel)
Vue.component('Progress', Progress)
Vue.component('Table', Table)

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: {
    App,
    Icon,
    Button,
    Panel,
    Progress,
    Table
  },
  template: '<App/>',
  render: h => h(App)
})
