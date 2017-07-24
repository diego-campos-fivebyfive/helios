'use strict'

const request = require('request-promise')
const { config } = require('../config')

const { auth } = config.isquik

const getAuthentication = () => request({
  method: 'POST',
  uri: `https://www.googleapis.com/identitytoolkit/v3/relyingparty/verifyPassword?key=${auth.key}`,
  json: {
    email: auth.mail,
    password: auth.password,
    returnSecureToken: true
  }
})

module.exports = {
  getAuthentication
}
