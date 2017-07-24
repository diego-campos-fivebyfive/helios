'use strict'

const request = require('request-promise')
const { config } = require('../config')

const { sices } = config

const getRequest = uri => request({ method: 'GET', uri }).then(x => JSON.parse(x))
const postRequest = (uri, data) => request({
  headers: { 'Content-Type': 'application/json' },
  method: 'POST',
  json: data,
  uri
})

const sendInveter = inverter => postRequest(`${sices.uri}/inverters`, inverter)
const sendStructure = structure => postRequest(`${sices.uri}/structures`, structure)
const sendModule = module => postRequest(`${sices.uri}/modules`, module)
const sendMemorial = memorial => postRequest(`${sices.uri}/memorials`, memorial)
const sendAccount = account => postRequest(`${sices.uri}/accounts`, account)

const getAccount = id => getRequest(`${sices.uri}/accounts/${id}`)
const getUser = id => getRequest(`${sices.uri}/users/${id}`)

module.exports = {
  sendInveter,
  sendStructure,
  sendModule,
  sendMemorial,
  sendAccount,
  getAccount,
  getUser
}
