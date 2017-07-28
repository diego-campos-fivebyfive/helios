'use strict'

const request = require('request-promise')
const { config } = require('../config')

const { isquik } = config

const getAuthentication = () => request({
  method: 'POST',
  uri: `${isquik.uri}/auth`,
  json: {
    Chave: isquik.auth.key
    Dominio: isquik.auth.user
  }
})

module.exports = {
  getAuthentication
}
