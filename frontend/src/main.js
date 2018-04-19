import Vue from 'vue'
import App from '@/App'
import { router } from '@/router'
import { initGlobals, globalComponents } from '@/globals'

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
