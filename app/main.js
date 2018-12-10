import Vue from 'vue'

import App from 'theme'

import checkers from './checkers'
import plugins from './plugins'
import trackers from './trackers'

/* eslint-disable no-new */
Vue.use(plugins, ({ router, sockets }) => {
  new Vue({
    el: '#app',
    template: '<App/>',
    render: h => h(App),
    router,
    sockets,
    mounted() {
      Notification.requestPermission()
    }
  })
})
