import Check from '@/theme/collection/Check'
import Text from '@/theme/collection/Text'
import AccountSelect from '@/components/select/Accounts'

export default {
  id: {},
  name: {
    label: 'Nome',
    component: Text,
    style: {
      size: [1, 1, 1]
    }
  },
  amount: {
    label: 'Valor',
    component: Check,
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
