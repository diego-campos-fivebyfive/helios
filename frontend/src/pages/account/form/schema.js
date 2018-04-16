import Text from '@/theme/collection/Text'
import Checkbox from '@/theme/collection/Checkbox'
import Agent from './fields/Agent'
import Level from './fields/Level'
import ParentAccount from './fields/ParentAccount'
import State from './fields/State'

export default {
  id: {},
  document: {
    label: 'CNPJ *',
    component: Text,
    style: {
      size: [1, 1, 1]
    }
  },
  extraDocument: {
    label: 'Inscrição Estadual',
    component: Text,
    style: {
      size: [1, 1, 1]
    }
  },
  lastname: {
    label: 'Razão Social *',
    component: Text,
    style: {
      size: [1, 1, 1]
    }
  },
  firstname: {
    label: 'Nome Fantasia *',
    component: Text,
    style: {
      size: [1, 1, 1]
    }
  },
  email: {
    label: 'E-mail *',
    component: Text,
    style: {
      size: [1, 1, 1]
    }
  },
  owner: {
    label: 'Pessoa para contato *',
    component: Text,
    style: {
      size: [1, 1, 1]
    }
  },
  phone: {
    label: 'Telefone *',
    component: Text,
    style: {
      size: [1, 1, 1]
    }
  },
  postcode: {
    label: 'CEP *',
    component: Text,
    style: {
      size: [1, 1, 1]
    }
  },
  state: {
    label: 'Estado *',
    component: State,
    style: {
      size: [1, 1, 1]
    }
  },
  city: {
    label: 'Cidade *',
    component: Text,
    style: {
      size: [1, 1, 1]
    }
  },
  district: {
    label: 'Bairro *',
    component: Text,
    style: {
      size: [1, 1, 1]
    }
  },
  street: {
    label: 'Logradouro *',
    component: Text,
    style: {
      size: [1, 1, 1]
    }
  },
  number: {
    label: 'Número *',
    component: Text,
    style: {
      size: [1, 1, 1]
    }
  },
  parentAccount: {
    label: 'Conta Mãe',
    component: ParentAccount,
    style: {
      size: [1, 1, 1]
    }
  },
  level: {
    label: 'Nivel de Conta *',
    component: Level,
    style: {
      size: [1, 1, 1]
    }
  },
  agent: {
    label: 'Agente Comercial *',
    component: Agent,
    style: {
      size: [1, 1, 1]
    }
  },
  persistent: {
    label: '',
    // eslint-disable-next-line max-len
    description: 'Esta conta não será bloqueada nem sofrerá alteração de nível automaticamente',
    // eslint-enable-next-line max-len
    component: Checkbox,
    style: {
      size: [1, 1, 1]
    }
  }
}
