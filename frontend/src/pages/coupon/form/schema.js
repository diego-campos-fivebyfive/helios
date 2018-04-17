import Check from '@/theme/collection/Check'
import Text from '@/theme/collection/Text'
import AccountSelect from './fields/Accounts'

export default {
  id: {},
  name: {
    label: 'Nome',
    component: Text,
    required: true,
    style: {
      size: [1, 1, 1]
    }
  },
  amount: {
    label: 'Valor',
    component: Check,
    required: true,
    type: 'money',
    exception: 'Formato de moeda inválido',
    style: {
      size: [1, 1, 1]
    }
  },
  account: {
    label: 'Conta',
    component: AccountSelect,
    style: {
      size: [1, 1, 2]
    }
  }
}
