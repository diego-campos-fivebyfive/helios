'use strict'

const Google = require('./google')
const request = require('request-promise')
const { config } = require('../config')

const { isquik } = config

const getRequest = uri => Google.getAuthentication().then(auth => (
  request({
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${auth.access_token}`
    },
    method: 'GET',
    uri
  })
    .then(x => JSON.parse(x))
))

const getAccount = id => getRequest(
  `https://api.isquik.com/isquik-dev/integradores/cadastrointegradores/ExporIntegrador/?Id=${id}`
)
const getMemorial = id => getRequest(`${isquik.uri}/memorial/${id}`)
const getProduct = code => getRequest(`${isquik.uri}/product/${code}`)

module.exports = {
  getMemorial,
  getProduct,
  getAccount
}
