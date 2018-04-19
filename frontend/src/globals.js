import { axios } from '@/router'
import ThemeCollection from '@/theme/collection'

export const globalComponents = Object.assign(ThemeCollection, {})

const globalVariables = [
  { name: 'user', response: axios.get('api/v1/user') }
]

const setComponents = (components, Vue) =>
  Object
    .entries(components)
    .map(([name, component]) => (
      new Promise(resolve => {
        resolve(Vue.component(name, component))
      })
    ))

const setVariables = Vue => globals => {
  /* eslint-disable no-param-reassign */
  Vue.prototype.$global = globals
  /* eslint-enable no-param-reassign */
}

const joinVariables = names =>
  responses =>
    responses.reduce((acc, { data }, index) => {
      const name = names[index]
      acc[name] = data
      return acc
    }, {})

const requestVariables = responses => Promise.all(responses)

export const initGlobals = async Vue => {
  const responses = globalVariables.map(x => x.response)
  const names = globalVariables.map(x => x.name)

  await requestVariables(responses)
    .then(joinVariables(names))
    .then(setVariables(Vue))

  return Promise.all(setComponents(globalComponents, Vue))
}
