'use strict'

const request = require('request-promise')
const { isquik } = require('../config')

const getAuthentication = () => request({
  uri: 'https://api.isquik.com/auth',
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  json: {
    Chave: isquik.auth.key,
    Dominio: isquik.auth.user
  }
})

const getRequest = uri => getAuthentication().then(auth => (
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

const getAccount = id =>
  getRequest(`${isquik.uri}/isquik-dev/integradores/cadastrointegradores/ExporIntegrador/?Id=${id}`)

const getUser = id =>
  getRequest(`${isquik.uri}/isquik-dev/integradores/cadastrointegradores/ExporContato/?Id=${id}`)

const getMemorial = id =>
  getRequest(`${isquik.uri}/isquik-dev/tabelabase/tabelabase/ExporTabelaBase?Id=${id}`)

const getProduct = id =>
  getRequest(`${isquik.uri}/isquik-dev/tabelabase/tabelabase/ExporProduto?Id=${id}`)

const getOrder = id =>
  getRequest(`${isquik.uri}/isquik-dev/orcamentovendas/orcamentovendas/ExporOrcamento/?Id=${id}`)

module.exports = {
  getMemorial,
  getProduct,
  getAccount,
  getOrder,
  getUser
}
