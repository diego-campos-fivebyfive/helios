import Button from '@/components/collection/Button'
import Input from '@/components/collection/Input'
import Modal from '@/components/collection/Modal'
import ModalConfirm from '@/components/collection/ModalConfirm'
import ModalForm from '@/components/collection/ModalForm'
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
Vue.component('Input', Input)
Vue.component('Button', Button)
Vue.component('Modal', Modal)
Vue.component('ModalConfirm', ModalConfirm)
Vue.component('ModalForm', ModalForm)
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
    Input,
    Modal,
    ModalConfirm,
    ModalForm,
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
