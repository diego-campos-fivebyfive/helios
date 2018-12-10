import components from 'helios/collection'

const install = Vue => {
  Object
    .entries(components)
    .forEach(([name, template]) => {
      Vue.component(name, template)
    })

  Vue.prototype.components = components
}

export default {
  install
}
