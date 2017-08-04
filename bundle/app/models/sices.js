'use strict'

const request = require('request-promise')
const { config } = require('../config')

const { sices } = config

const getRequest = uri => request({
  method: 'GET',
  uri
})
  .then(x => JSON.parse(x))

const postRequest = (uri, data) => request({
  headers: {
    'Content-Type': 'application/json'
  },
  method: 'POST',
  json: data,
  uri
})

const putRequest = (uri, data) => request({
  headers: {
    'Content-Type': 'application/json'
  },
  method: 'PUT',
  json: data,
  uri
})

const updateAccount = (id, account) => putRequest(`${sices.uri}/accounts/${id}`, account)
const updateUser = (id, user) => putRequest(`${sices.uri}/users/${id}`, user)
const sendInverter = inverter => postRequest(`${sices.uri}/inverters`, inverter)
const sendStructure = structure => postRequest(`${sices.uri}/structures`, structure)
const sendModule = module => postRequest(`${sices.uri}/modules`, module)
const sendStringbox = stringbox => postRequest(`${sices.uri}/stringboxes`, stringbox)
const sendVariety = variety => postRequest(`${sices.uri}/varieties`, variety)
const sendMemorial = memorial => postRequest(`${sices.uri}/memorials`, memorial)
const sendAccount = account => postRequest(`${sices.uri}/accounts`, account)
const sendUser = user => postRequest(`${sices.uri}/users`, user)
const getUser = id => getRequest(`${sices.uri}/users/${id}`)

module.exports = {
  updateAccount,
  updateUser,
  sendInverter,
  sendStructure,
  sendModule,
  sendStringbox,
  sendVariety,
  sendMemorial,
  sendAccount,
  sendUser,
  getUser
}
