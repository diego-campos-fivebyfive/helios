'use strict'
const request = require('request-promise')
const { app, config } = require('../config')
const { ISQUIK_API } = config

const sendRequest = (uri) => {
  let options = {
    method: 'GET'
  }
  options = Object.assign(options, { uri })
  return request(options).then((data) => JSON.parse(data))
}

const getProduct = (code) => sendRequest(`${ISQUIK_API}/product/${code}`)

module.exports = {
  getProduct
}
