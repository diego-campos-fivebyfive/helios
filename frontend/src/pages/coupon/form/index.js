import Form from '@/theme/collection/Form'
import Input from '@/theme/collection/Input'

import AccountSelect from '@/components/select/Accounts'

import actions from './actions'

export default {
  mixins: [Form],
  data: () => ({
    actions,
    params: {
      size: 'medium',
      cols: 2
    },
    payload: {
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
  })
}
