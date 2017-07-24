'use strict'

const express = require('express')
const bodyParser = require('body-parser')
const { sendResponse, preRequest } = require('./components')
const { bundle } = require('./config')

const app = express()
app.listen(bundle.port)
app.use(bundle.headers)
app.use(bodyParser.json())
app.use(bodyParser.urlencoded({ extended: true }))

module.exports = {
  sendResponse,
  router: app,
  preRequest
}
