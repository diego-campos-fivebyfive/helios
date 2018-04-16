import Text from '@/theme/collection/Text'
import Select from '@/theme/collection/Select'

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
    component: Select,
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
    component: Select,
    style: {
      size: [1, 1, 1]
    }
  },
  level: {
    label: 'Conta Mãe',
    component: Select,
    style: {
      size: [1, 1, 1]
    }
  },
  agent: {
    label: 'Conta Mãe',
    component: Select,
    style: {
      size: [1, 1, 1]
    }
  }
}
