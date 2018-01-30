import Icon from 'vue-awesome/components/Icon'
import 'vue-awesome/icons'

import Button from '@/components/collection/Button'
import Page from '@/components/collection/Page'
import Panel from '@/components/collection/Panel'
import Modal from '@/components/collection/Modal'
import Progress from '@/components/collection/Progress'
import Table from '@/components/collection/Table'

import Vue from 'vue'
import App from './App'
import router from './router'

Vue.component('Icon', Icon)
Vue.component('Button', Button)
Vue.component('Modal', Modal)
Vue.component('Page', Page)
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
    Modal,
    Page,
    Panel,
    Progress,
    Table
  },
  template: '<App/>',
  render: h => h(App)
})
