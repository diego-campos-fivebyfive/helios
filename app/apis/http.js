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
}

const handleErrorResponse = error => {
  const { data: errorData } = error.response
  if (errorData) {
    return Promise.reject(errorData)
  }

  throw new Error(`An unexpected request error has occurred: ${error}`)
}

axios.interceptors.response
  .use(handleSuccessResponse, handleErrorResponse)

export default axios
