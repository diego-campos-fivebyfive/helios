'use strict'

const BUNDLE_PORT = process.env.CES_BUNDLE_PORT

const config = {
  bundle: {
    headers: (request, response, next) => {
      response.header('Access-Control-Allow-Methods', 'GET, POST')
      response.header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With')
      response.header('Access-Control-Allow-Credentials', 'true')
      next()
    },
    port: BUNDLE_PORT
  }
}

module.exports = config
