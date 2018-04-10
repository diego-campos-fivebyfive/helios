import Form from '@/theme/collection/Form'
import actions from './actions'
import schema from './schema'

export default {
  mixins: [Form],
  data: () => ({
    actions,
    payload: schema
  })
}
