'use strict'
const express = require('express')
const bodyParser = require('body-parser')

const { sendResponse, preRequest } = require('./components')

const expressApp = () => {
  const app = express()
  app.listen(process.env.CES_BUNDLE_PORT)

  app.use(bodyParser.json())
  app.use(bodyParser.urlencoded({ extended: true }))

  app.use((request, response, next) => {
    response.header('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE')
    response.header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
    response.header('Access-Control-Allow-Credentials', 'true')
    next()
  })

  return app
}

const app = expressApp()
const router = app

module.exports = {
  sendResponse,
  preRequest,
  router
}
