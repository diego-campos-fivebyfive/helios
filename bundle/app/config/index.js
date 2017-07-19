const app = require('./app')

const SICES_HOST = process.env.CES_SICES_HOST
const SICES_PORT = process.env.CES_SICES_PORT
const ISQUIK_HOST = process.env.CES_ISQUIK_HOST
const ISQUIK_PORT = process.env.CES_ISQUIK_PORT

const auth = {
  key: 'AIzaSyCDKjcT0Rme97CLoZtA-WYKDdAch-yc2gQ',
  mail: 'sices.solar@sices.com.br',
  password: 'sices123@!'
}

const config = {
  SICES_API: `${SICES_HOST}:${SICES_PORT}/api`,
  ISQUIK_API: `${ISQUIK_HOST}:${ISQUIK_PORT}`
}

module.exports = {
  app,
  config: Object.assign(config, { auth })
}
