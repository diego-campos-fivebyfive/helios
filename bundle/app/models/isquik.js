'use strict'
const Google = require('./google')
const request = require('request-promise')
const { app, config } = require('../config')
const { isquik } = config

const sendRequest = (uri) => Google.getAuthentication().then((auth) => (
  request({
    method: 'GET', uri, qs: { auth: auth.idToken }
  })
  .then((x) => JSON.parse(x))
))

const getAccount = (id) => sendRequest(`${isquik.uri}/user/${id}`)
const getMemorial = (id) => sendRequest(`${isquik.uri}/memorial/${id}`)
const getProduct = (code) => sendRequest(`${isquik.uri}/product/${code}`)

module.exports = {
  getMemorial,
  getProduct,
  getAccount
}
