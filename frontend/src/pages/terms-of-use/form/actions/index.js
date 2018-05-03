import Create from './Create'
import Edit from './Edit'

const actions = {
  default: {
    title: 'Gerenciamento de Termo',
    layout: {
      columns: {
        size: 'small',
        total: 1
      }
    }
  },
  create: {
    title: 'Cadastro de Termo de uso',
    component: Create
  },
  edit: {
    title: 'Edição de Termo de uso',
    component: Edit
  }
}

export default actions
