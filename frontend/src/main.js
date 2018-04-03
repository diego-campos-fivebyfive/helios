import Vue from 'vue'
import App from '@/App'
import router from '@/router'
import ThemeCollection from '@/theme/collection'

import AccountSelect from '@/components/select/Accounts'

const components = Object.assign(ThemeCollection, {
  AccountSelect
})

Object.entries(components)
  .forEach(([name, component]) => {
    Vue.component(name, component)
  })

/* eslint-disable no-new, no-console */
new Vue({
  el: '#app',
  router,
  components,
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
