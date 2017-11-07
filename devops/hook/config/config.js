'use strict'

const HOOK_PORT = process.env.SICES_HOOK_PORT

const acl = {
  methods: 'Access-Control-Allow-Methods',
  headers: 'Access-Control-Allow-Headers',
  credentials: 'Access-Control-Allow-Credentials'
}

const bundle = {
  headers: (request, response, next) => {
    response.header(acl.methods, 'GET, POST')
    response.header(acl.headers, 'Content-Type, X-Requested-With')
    response.header(acl.credentials, 'true')
    next()
  },
  port: HOOK_PORT
}

module.exports = {
  bundle
}
