import locale from 'locale'

const install = Vue => {
  Vue.prototype.$locale = locale
}

export default {
  install
}
