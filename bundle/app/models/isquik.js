'use strict'
const Google = require('./google')
const request = require('request-promise')
const { config } = require('../config')
const { isquik } = config

const getRequest = (uri) => Google.getAuthentication().then((auth) => (
  request({
    method: 'GET',
    qs: {
      auth: auth.idToken
    },
    uri
  })
  .then((x) => JSON.parse(x))
))

const getAccount = (id) => getRequest(`${isquik.uri}/user/${id}`)
const getMemorial = (id) => getRequest(`${isquik.uri}/memorial/${id}`)
const getProduct = (code) => getRequest(`${isquik.uri}/product/${code}`)

module.exports = {
  getMemorial,
  getProduct,
  getAccount
}
