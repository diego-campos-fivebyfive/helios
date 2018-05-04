import Vue from 'vue'
import VueSocket from 'vue-socket.io'
import VueMoment from 'vue-momentjs'
import moment from 'moment'

import App from '@/App'
import { router } from '@/router'
import { initGlobals, globalComponents } from '@/globals'

initGlobals(Vue).then(() => {
  const userId = Vue.prototype.$global.user.id

  Vue.use(VueMoment, moment)
  Vue.use(VueSocket, `${process.env.SOCKET_URL}/socket?id=${userId}`)

  /* eslint-disable no-new, no-console */
  new Vue({
    el: '#app',
    router,
    components: globalComponents,
    template: '<App/>',
    render: h => h(App),
    sockets: {
      connect() {
        if (process.env.AMBIENCE === 'development') {
          console.log('socket connected')
        }
      }
    },
    mounted() {
      if (process.env.AMBIENCE === 'development') {
        this.$cookies.remove('PHPSESSID')
        this.$cookies.set('PHPSESSID', process.env.PHPSESSID)
        console.log(`PHPSESSID: ${this.$cookies.get('PHPSESSID')}`)
      }
    }
  })
})
