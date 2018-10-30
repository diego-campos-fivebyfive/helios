import quickAccess from '@/../theme/quick-access'

const generateInitialStatesFromQuickAccessKeys = () =>
  Object.keys(quickAccess).reduce((acc, item) => (acc[item] = 0, acc), {})

export default {
  namespaced: true,
  state: generateInitialStatesFromQuickAccessKeys(),
  mutations: {
    setContent(state, { toQuickAccessKey, content }) {
      state[toQuickAccessKey] = content
    }
  },
  actions: {
    requestContent({ commit }, toQuickAccessKey) {
      quickAccess[toQuickAccessKey].getInitial()
        .then(content => {
          commit('setContent', { toQuickAccessKey, content })
        })
    }
  }
}
