import Form from '@/theme/collection/Form'
import Input from '@/theme/collection/Input'
import AccountSelect from '@/components/select/Accounts'

import Actions from './Actions'

export default {
  mixins: [Form],
  data: () => ({
    action: {
      component: Actions,
      current: 'create',
      titles: {
        edit: 'Edição de Cupom',
        create: 'Cadastro de Cupom'
      }
    },
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
