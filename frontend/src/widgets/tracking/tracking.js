import { axios } from '@/router'
import { intercom } from './intercom'
import { woopra } from './woopra'

export const tracking = () => {
  axios.get('api/v1/track-account')
    .then(({ data }) => {
      intercom(data)
      woopra(data)
    })
}
