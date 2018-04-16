import FormModal from '@/theme/collection/FormModal'
import actions from './actions'
import schema from './schema'

export default {
  mixins: [FormModal],
  data: () => ({
    actions,
    schema
  })
}
