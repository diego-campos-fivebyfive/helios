'use strict'
const request = require('request-promise')
const { app, config } = require('../config')
const { SICES_API } = config

const sendRequest = (uri, data) => {
  let options = {
    method: 'POST',
    body: data,
    json: true,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': '123'
    }
  }
  options = Object.assign(options, uri)
  request(options)
}

const sendInveter = (product) => sendRequest(`${SICES_API}/inverters/${product.code}`, product)
const sendStructure = (product) => sendRequest(`${SICES_API}/structure/${product.code}`, product)
const sendModule = (product) => sendRequest(`${SICES_API}/module/${product.code}`, product)

module.exports = {
  sendInveter,
  sendStructure,
  sendModule
}
