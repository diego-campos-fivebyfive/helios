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

const get = uri => getAuthentication().then(auth => (
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

const post = (uri, data) => getAuthentication().then(auth => (
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

const put = (uri, data) => getAuthentication().then(auth => (
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

const del = uri => getAuthentication().then(auth => (
  request({
    resolveWithFullResponse: true,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${auth.access_token}`
    },
    method: 'DELETE',
    uri
  })
    .then(({ body, statusCode }) => ({
      ...JSON.parse(body),
      statusCode
    }))
))

const sendAccount = account => post(`${sices.uri}/accounts`, account)
const updateAccount = (id, account) => put(`${sices.uri}/accounts/${id}`, account)

const getUser = id => get(`${sices.uri}/users/${id}`)
const sendUser = user => post(`${sices.uri}/users`, user)
const updateUser = (id, user) => put(`${sices.uri}/users/${id}`, user)

const getOrder = id => get(`${sices.uri}/orders/${id}`)
const sendOrder = order => post(`${sices.uri}/orders`, order)
const updateOrder = (id, order) => put(`${sices.uri}/orders/${id}`, order)
const deleteOrder = id => del(`${sices.uri}/orders/${id}`)

const sendMemorial = memorial => post(`${sices.uri}/memorials`, memorial)
const sendInverter = inverter => post(`${sices.uri}/inverters`, inverter)
const sendStructure = structure => post(`${sices.uri}/structures`, structure)
const sendModule = module => post(`${sices.uri}/modules`, module)
const sendStringbox = stringbox => post(`${sices.uri}/stringboxes`, stringbox)
const sendVariety = variety => post(`${sices.uri}/varieties`, variety)

module.exports = {
  sendAccount,
  updateAccount,
  getUser,
  sendUser,
  updateUser,
  getOrder,
  sendOrder,
  updateOrder,
  deleteOrder,
  sendMemorial,
  sendInverter,
  sendStructure,
  sendModule,
  sendStringbox,
  sendVariety
}
