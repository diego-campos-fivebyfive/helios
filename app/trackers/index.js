import { http } from 'apis'

import { intercom } from './intercom'
import { woopra } from './woopra'

const tracking = () => {
  if (
    process.env.PLATFORM === 'web'
    && process.env.CLIENT === 'integrador'
    && process.env.NODE_ENV === 'production'
  ) {
    http.get('api/v1/track_account')
      .then(({ data }) => {
        intercom(data)
        woopra(data)
      })
  }
}

export default tracking
