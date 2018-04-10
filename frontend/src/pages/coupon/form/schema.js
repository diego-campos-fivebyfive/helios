import Input from '@/theme/collection/Input'
import AccountSelect from '@/components/select/Accounts'

export default {
  id: {},
  name: {
    label: 'Nome',
    component: Input,
    style: {
      size: [1, 1, 1]
    }
  },
  amount: {
    label: 'Valor',
    component: Input,
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
