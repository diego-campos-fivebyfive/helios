'use strict'

const request = require('request-promise')
const { isquik } = require('../config')

const getAuthentication = () => request({
  uri: 'https://api.isquik.com/auth',
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  json: {
    Chave: isquik.auth.key,
    Dominio: isquik.auth.user
  }
})

module.exports = {
  getAuthentication
}
