import Form from '@/theme/collection/Form'
import Input from '@/theme/collection/Input'
import AccountSelect from '@/components/select/Accounts'

import actions from './actions'

export default {
  mixins: [Form],
  data: () => ({
    actions,
    payload: {
      id: {},
      name: {
        label: 'Nome',
        component: Input
      },
      amount: {
        label: 'Valor',
        component: Input,
        type: 'money',
        exception: 'Formato de moeda inválido'
      },
      account: {
        label: 'Conta',
        component: AccountSelect
      }
    }
  })
}
