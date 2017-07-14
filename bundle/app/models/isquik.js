'use strict'
const request = require('request-promise')
const { app } = require('../config')
const { router } = app

const getProduct = (code) => {
  const options = {
    method: 'GET',
    uri: `http://localhost:2001/product/${code}`
  }
  return request(options).then((data) => JSON.parse(data))
}

module.exports = {
  getProduct
}
