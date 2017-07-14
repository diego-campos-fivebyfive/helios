'use strict'
const request = require('request-promise')
const { app } = require('../config')
const { router } = app

const getNotificationData = (id) => {
  const options = {
    method: 'GET',
    uri: `http://localhost:2001/notification/:${id}`
  }
  return request(options)
}

module.exports = {
  getNotificationData
}
