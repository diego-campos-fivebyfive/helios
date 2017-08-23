'use strict'

const request = require('request-promise')
const { sices } = require('../config')

const getAuthentication = () => request({
  resolveWithFullResponse: true,
  uri: `${sices.host}/oauth/v2/token`,
  method: 'POST',
  formData: {
    client_id: '1_2b14bvyiqfr4wksgcc0ck004w8og4wkwc4o4gksk4ks8oooo8g',
    client_secret: '53xlex74i4kk8ckww08goc8oocw048s0wwcok84040w0kcw0ks',
    grant_type: 'client_credentials'
  }
})
  .then(({ body, statusCode }) => ({
    ...JSON.parse(body),
    statusCode
  }))

const getRequest = uri => getAuthentication().then(auth => (
  request({
    resolveWithFullResponse: true,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${auth.access_token}`
    },
    method: 'GET',
    uri
  })
    .then(({ body, statusCode }) => ({
      ...JSON.parse(body),
      statusCode
    }))
))

const postRequest = (uri, data) => getAuthentication().then(auth => (
  request({
    resolveWithFullResponse: true,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${auth.access_token}`
    },
    method: 'POST',
    json: data,
    uri
  })
    .then(({ body, statusCode }) => ({
      ...body,
      statusCode
    }))
))

const putRequest = (uri, data) => getAuthentication().then(auth => (
  request({
    resolveWithFullResponse: true,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${auth.access_token}`
    },
    method: 'PUT',
    json: data,
    uri
  })
    .then(({ body, statusCode }) => ({
      ...body,
      statusCode
    }))
))

const updateAccount = (id, account) => putRequest(`${sices.uri}/accounts/${id}`, account)
const updateUser = (id, user) => putRequest(`${sices.uri}/users/${id}`, user)
const sendInverter = inverter => postRequest(`${sices.uri}/inverters`, inverter)
const sendStructure = structure => postRequest(`${sices.uri}/structures`, structure)
const sendModule = module => postRequest(`${sices.uri}/modules`, module)
const sendStringbox = stringbox => postRequest(`${sices.uri}/stringboxes`, stringbox)
const sendVariety = variety => postRequest(`${sices.uri}/varieties`, variety)
const sendMemorial = memorial => postRequest(`${sices.uri}/memorials`, memorial)
const sendAccount = account => postRequest(`${sices.uri}/accounts`, account)
const sendOrder = order => postRequest(`${sices.uri}/orders`, order)
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
  sendOrder,
  sendUser,
  getUser
}
