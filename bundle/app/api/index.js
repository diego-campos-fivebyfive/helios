'use strict'
const { app } = require('../config')
const { router, sendResponse } = app

const product = require('./product')
const memorial = require('./memorial')

router.post('/api/v1/notification', ((request, response) => {
  const { body, callback } = request.body
  const requestCopy = request
  let action

  switch (callback) {
    case 'product_create':
      action = product.send
      requestCopy.body = body.codes
      break

    case 'memorial_create':
      action = memorial.send
      requestCopy.body = body.codes
      break

    default:
      response.status(404).end()
      return
  }

  sendResponse(requestCopy, response, action)
}))
