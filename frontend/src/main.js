import Vue from 'vue'
import VueMoment from 'vue-momentjs'
import moment from 'moment'

import App from '@/App'
import { router } from '@/router'
import { initGlobals, globalComponents } from '@/globals'

Vue.use(VueMoment, moment)

initGlobals(Vue).then(() => {
  /* eslint-disable no-new, no-console */
  new Vue({
    el: '#app',
    router,
    components: globalComponents,
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
})
