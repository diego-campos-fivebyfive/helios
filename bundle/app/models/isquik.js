'use strict'

const Google = require('./google')
const request = require('request-promise')
const { isquik } = require('../config')

const getRequest = uri => Google.getAuthentication().then(auth => (
  request({
    resolveWithFullResponse: true,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${auth.access_token}`
    },
    method: 'GET',
    uri
  })
    .then(({ body, statusCode }) => ({
      ...JSON.parse(body),
      statusCode
    }))
))

const getAccount = id => getRequest(
  `https://api.isquik.com/isquik-dev/integradores/cadastrointegradores/ExporIntegrador/?Id=${id}`
)

const getUser = id => getRequest(
  `https://api.isquik.com/isquik-dev/integradores/cadastrointegradores/ExporContato/?Id=${id}`
)

const getMemorial = id => getRequest(
  `https://api.isquik.com/isquik-dev/tabelabase/tabelabase/ExporTabelaBase?Id=${id}`
)

const getProduct = code => getRequest(`${isquik.uri}/product/${code}`)

module.exports = {
  getMemorial,
  getProduct,
  getAccount,
  getUser
}
