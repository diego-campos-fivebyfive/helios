import Text from '@/theme/collection/Text'
import Datepicker from '@/theme/collection/Datepicker'

export default {
  id: {},
  publishedAt: {
    label: 'Publicado em',
      component: Datepicker,
      required: true,
      style: {
      size: [1, 1, 1]
    }
  },
  title: {
    label: 'Titulo',
    component: Text,
    required: true,
    style: {
      size: [1, 1, 1]
    }
  },
  url: {
    label: 'Url',
    component: Text,
    required: true,
    style: {
      size: [1, 1, 1]
    }
  }
}
