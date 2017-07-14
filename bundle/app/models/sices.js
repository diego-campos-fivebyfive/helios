'use strict'
const request = require('request-promise')
const { app } = require('../config')
const { router } = app

const sendInveter = () => {
  const options = {
    method: 'POST',
    uri: `http://localhost:8000/api/v1/inverter/:${notification.code}`,
    body: notification,
    json: true,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': '123'
    }
  }
  return request(options)
}

module.exports = {
  sendInveter
}
