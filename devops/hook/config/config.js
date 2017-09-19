'use strict'

const HOOK_PORT = process.env.SICES_HOOK_PORT

const config = {
  bundle: {
    headers: (request, response, next) => {
      response.header('Access-Control-Allow-Methods', 'GET, POST')
      response.header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With')
      response.header('Access-Control-Allow-Credentials', 'true')
      next()
    },
    port: HOOK_PORT
  }
}

module.exports = config
