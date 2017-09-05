'use strict'

const request = require('request-promise')
const { isquik } = require('../config')

const parserResponse = ({ body, statusCode }) => ({
  body: { ...JSON.parse(body) },
  statusCode
})

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
    .then(data => new Promise((resolve, reject) => {
      const { body, statusCode } = parserResponse(data)

      if (!body.error) {
        resolve({
          ...body,
          statusCode
        })
        return
      }

      reject({
        title: 'Isquik API exception',
        message: body,
        method: 'GET',
        statusCode,
        uri
      })
    }))
))

const getAccount = id =>
  getRequest(`${isquik.uri}/integradores/cadastrointegradores/ExporIntegrador/?Id=${id}`)

const getUser = id =>
  getRequest(`${isquik.uri}/integradores/cadastrointegradores/ExporContato/?Id=${id}`)

const getMemorial = id =>
  getRequest(`${isquik.uri}/tabelabase/tabelabase/ExporTabelaBase?Id=${id}`)

const getProduct = id =>
  getRequest(`${isquik.uri}/tabelabase/tabelabase/ExporProduto?Id=${id}`)

const getOrder = id =>
  getRequest(`${isquik.uri}/orcamentovendas/orcamentovendas/ExporOrcamento/?Id=${id}`)

module.exports = {
  getMemorial,
  getProduct,
  getAccount,
  getOrder,
  getUser
}
