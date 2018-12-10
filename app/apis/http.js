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

  return fetch(`${process.env.API_URL}/check-online`)
    .then(({ redirected, url }) => {
      if (
        redirected
        && url === `${process.env.API_URL}/login`
      ) {
        localStorage.clear()

        window.location = process.env.PLATFORM === 'web'
          ? process.env.API_URL
          : '#/login'

        return
      }

      throw new Error('A non JSON response was found in Axios request')
    })
}

const handleErrorResponse = () => {
  throw new Error('An unexpected request error has occurred')
}

axios.interceptors.response
  .use(handleSuccessResponse, handleErrorResponse)

export default axios
