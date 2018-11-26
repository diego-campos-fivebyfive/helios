import moment from 'moment'

const install = Vue => {
  Vue.prototype.moment = moment
  Vue.prototype.$moment = moment
}

export default {
  install
}
