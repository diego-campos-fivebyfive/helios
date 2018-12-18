import Vue from 'vue'

import App from 'theme'

import plugins from './plugins'
import trackers from './trackers'

/* eslint-disable no-new */
Vue.use(plugins, options => {
  new Vue(Object.assign({
    el: '#app',
    template: '<App/>',
    render: h => h(App),
    mounted() {
      if (process.env.PLATFORM === 'web') {
        Notification.requestPermission()
      }
    }
  }, options))
})
