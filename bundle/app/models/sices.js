'use strict'
const request = require('request-promise')
const { app, config } = require('../config')
const { sices } = config

const sendRequest = (uri, data) => request({
  headers: { 'Content-Type': 'application/json' },
  method: 'POST',
  json: data,
  uri
})

const sendInveter = (product) => sendRequest(`${sices.uri}/inverters`, product)
const sendStructure = (product) => sendRequest(`${sices.uri}/structures`, product)
const sendModule = (product) => sendRequest(`${sices.uri}/modules`, product)
const sendMemorial = (memorial) => sendRequest(`${sices.uri}/memorials`, memorial)

module.exports = {
  sendInveter,
  sendStructure,
  sendModule,
  sendMemorial
}
