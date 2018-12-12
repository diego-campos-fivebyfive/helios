import * as dateFilters from './date'

const install = Vue => {
  Object
    .entries(dateFilters)
    .forEach(([name, fn]) => {
      Vue.filter(name, fn)
    })
}

export default {
  install
}
