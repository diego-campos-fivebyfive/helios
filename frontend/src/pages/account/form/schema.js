import Mask from '@/theme/collection/Mask'
import Text from '@/theme/collection/Text'
import Checkbox from '@/theme/collection/Checkbox'
import Agents from './fields/Agents'
import Levels from './fields/Levels'
import ParentAccounts from './fields/ParentAccounts'
import States from './fields/States'

export const schema = {
  id: {},
  document: {
    label: 'CNPJ',
    component: Mask,
    type: 'cnpj',
    required: true,
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
    label: 'Razão Social',
    component: Text,
    required: true,
    style: {
      size: [1, 1, 1]
    }
  },
  firstname: {
    label: 'Nome Fantasia',
    component: Text,
    required: true,
    style: {
      size: [1, 1, 1]
    }
  },
  email: {
    label: 'E-mail',
    component: Text,
    required: true,
    style: {
      size: [1, 1, 1]
    }
  },
  owner: {
    label: 'Pessoa para contato',
    component: Text,
    placeholder: 'Nome',
    required: true,
    style: {
      size: [1, 1, 1]
    }
  },
  phone: {
    label: 'Telefone',
    component: Mask,
    type: 'phone',
    required: true,
    style: {
      size: [1, 1, 1]
    }
  },
  postcode: {
    label: 'CEP',
    component: Mask,
    type: 'postcode',
    required: true,
    style: {
      size: [1, 1, 1]
    }
  },
  state: {
    label: 'Estado',
    component: States,
    required: true,
    style: {
      size: [1, 1, 1]
    }
  },
  city: {
    label: 'Cidade',
    component: Text,
    required: true,
    style: {
      size: [1, 1, 1]
    }
  },
  district: {
    label: 'Bairro',
    component: Text,
    required: true,
    style: {
      size: [1, 1, 1]
    }
  },
  street: {
    label: 'Logradouro',
    component: Text,
    required: true,
    style: {
      size: [1, 1, 1]
    }
  },
  number: {
    label: 'Número',
    component: Text,
    required: true,
    style: {
      size: [1, 1, 1]
    }
  },
  parentAccount: {
    schemaID: 'PA01',
    label: 'Conta Mãe',
    component: ParentAccounts,
    style: {
      size: [1, 1, 1]
    }
  },
  level: {
    label: 'Nivel de Conta',
    component: Levels,
    required: true,
    style: {
      size: [1, 1, 1]
    },
    disabled: {
      manager: 'PA01',
      state: false
    }
  },
  agent: {
    label: 'Agente Comercial',
    component: Agents,
    required: true,
    style: {
      size: [1, 1, 1]
    },
    disabled: {
      manager: 'PA01',
      state: false
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
