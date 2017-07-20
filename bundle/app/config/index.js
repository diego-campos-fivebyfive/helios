const app = require('./app')

const SICES_HOST = process.env.CES_SICES_HOST
const SICES_PORT = process.env.CES_SICES_PORT
const ISQUIK_HOST = process.env.CES_ISQUIK_HOST
const ISQUIK_PORT = process.env.CES_ISQUIK_PORT
const ISQUIK_AUTH_KEY = process.env.CES_ISQUIK_AUTH_KEY
const ISQUIK_AUTH_USER = process.env.CES_ISQUIK_AUTH_USER
const ISQUIK_AUTH_PASS = process.env.CES_ISQUIK_AUTH_PASS

const config = {
  sices: {
    uri: `${SICES_HOST}:${SICES_PORT}/api`
  },
  isquik: {
    uri: `${ISQUIK_HOST}:${ISQUIK_PORT}`,
    auth: {
      password: ISQUIK_AUTH_PASS',
      mail: ISQUIK_AUTH_USER,
      key: ISQUIK_AUTH_KEY
    }
  }
}

module.exports = {
  config,
  app
}
