'use strict'
const request = require('request-promise')
const { app, config } = require('../config')
const { ISQUIK_API } = config

const sendRequest = (uri) => request({ method: 'GET', uri }).then((x) => JSON.parse(x))
const getProduct = (code) => sendRequest(`${ISQUIK_API}/product/${code}`)
module.exports = {
  getProduct,
  getMemorial
}
