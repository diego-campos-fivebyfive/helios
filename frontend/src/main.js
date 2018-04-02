import Button from '@/components/collection/Button'
import Form from '@/components/collection/Form'
import Input from '@/components/collection/Input'
import Modal from '@/components/collection/Modal'
import ModalConfirm from '@/components/collection/ModalConfirm'
import Notification from '@/components/collection/Notification'
import Paginator from '@/components/collection/Paginator'
import Panel from '@/components/collection/Panel'
import Progress from '@/components/collection/Progress'
import Select from '@/components/collection/Select'
import Table from '@/components/collection/Table'

import Icon from 'vue-awesome/components/Icon'
import '@/assets/script/icons'

import Vue from 'vue'
import App from './App'
import router from './router'

Vue.component('Icon', Icon)

Vue.component('Button', Button)
Vue.component('Form', Form)
Vue.component('Input', Input)
Vue.component('Modal', Modal)
Vue.component('ModalConfirm', ModalConfirm)
Vue.component('Notification', Notification)
Vue.component('Paginator', Paginator)
Vue.component('Panel', Panel)
Vue.component('Progress', Progress)
Vue.component('Select', Select)
Vue.component('Table', Table)

/* eslint-disable no-new, no-console */
new Vue({
  el: '#app',
  router,
  components: {
    App,
    Button,
    Form,
    Input,
    Modal,
    ModalConfirm,
    Notification,
    Paginator,
    Panel,
    Progress,
    Select,
    Table
  },
  template: '<App/>',
  render: h => h(App),
  mounted() {
    if (process.env.AMBIENCE === 'development') {
      this.$cookies.remove('PHPSESSID')
      this.$cookies.set('PHPSESSID', process.env.PHPSESSID)
      console.log(`PHPSESSID: ${this.$cookies.get('PHPSESSID')}`)
    }
  }
})
