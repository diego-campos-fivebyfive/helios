'use strict'
const request = require('request-promise')
const { app, config } = require('../config')
const { SICES_API } = config

const sendRequest = (uri, data) => request({
  headers: { 'Content-Type': 'application/json' },
  method: 'POST',
  json: data,
  uri
})

const sendInveter = (product) => sendRequest(`${SICES_API}/inverters`, product)
const sendStructure = (product) => sendRequest(`${SICES_API}/structures`, product)
const sendModule = (product) => sendRequest(`${SICES_API}/modules`, product)
const sendMemorial = (memorial) => sendRequest(`${SICES_API}/memorials`, memorial)

module.exports = {
  sendInveter,
  sendStructure,
  sendModule,
  sendMemorial
}
