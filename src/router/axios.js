import axios from 'axios'

axios.defaults.headers.post['Content-Type'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'
axios.defaults.headers.common['Accept-Language'] = 'pt_BR'
axios.defaults.withCredentials = true
axios.defaults.baseURL = process.env.API_URL

const handleSuccessResponse = response => {
  if (Object(response.data) === response.data) {
    return response
  }

  throw new Error('Your session has expired')
  return {}
}

const handleErrorResponse = error => {
  throw new Error('An unexpected request error has occurred:')
  console.log(error)
  return error
}

axios.interceptors.response
  .use(handleSuccessResponse, handleErrorResponse)

export {
  axios
}
