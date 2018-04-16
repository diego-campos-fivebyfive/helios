import Create from './Create'
import Edit from './Edit'

const actions = {
  default: {
    title: 'Gerenciamento de Cupom',
    layout: {
      columns: {
        size: 'medium',
        total: 2
      }
    }
  },
  create: {
    title: 'Cadastro de Cupom',
    component: Create
  },
  edit: {
    title: 'Edição de Cupom',
    component: Edit
  }
}

export default actions
