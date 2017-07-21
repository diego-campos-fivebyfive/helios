'use strict'
const request = require('request-promise')
const { config } = require('../config')
const { sices } = config

const sendRequest = (uri, data) => request({
  headers: { 'Content-Type': 'application/json' },
  method: 'POST',
  json: data,
  uri
})

const sendInveter = (inverter) => sendRequest(`${sices.uri}/inverters`, inverter)
const sendStructure = (structure) => sendRequest(`${sices.uri}/structures`, structure)
const sendModule = (module) => sendRequest(`${sices.uri}/modules`, module)
const sendMemorial = (memorial) => sendRequest(`${sices.uri}/memorials`, memorial)
const sendAccount = (account) => sendRequest(`${sices.uri}/accounts`, account)

module.exports = {
  sendInveter,
  sendStructure,
  sendModule,
  sendMemorial,
  sendAccount
}
