import Icon from 'vue-awesome/components/Icon'
import 'vue-awesome/icons'

import Vue from 'vue'
import App from './App'
import router from './router'

Vue.component('icon', Icon)

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: { App, Icon },
  template: '<App/>'
})
