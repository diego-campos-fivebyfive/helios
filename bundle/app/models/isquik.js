'use strict'
const request = require('request-promise')
const { app, config } = require('../config')
const { isquik } = config

const sendRequest = (uri) => request({ method: 'GET', uri }).then((x) => JSON.parse(x))

const getMemorial = (id) => sendRequest(`${isquik.uri}/memorial/${id}`)
const getProduct = (code) => sendRequest(`${isquik.uri}/product/${code}`)

module.exports = {
  getMemorial,
  getProduct
}
