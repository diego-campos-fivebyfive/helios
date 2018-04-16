import Create from './Create'
import Edit from './Edit'

const actions = {
  default: {
    title: 'Gerenciamento de Contas',
    layout: {
      columns: {
        size: 'large',
        total: 3
      }
    }
  },
  create: {
    title: 'Cadastro de Conta',
    component: Create
  },
  edit: {
    title: 'Edição de Conta',
    component: Edit
  }
}

export default actions
