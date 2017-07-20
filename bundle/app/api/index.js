'use strict'
const { app } = require('../config')
const { router, sendResponse } = app

const product = require('./product')
const memorial = require('./memorial')
const account = require('./account')

router.post('/api/v1/notifications', ((request, response) => {
  const { body, callback } = request.body
  const requestCopy = request
  let action

  switch (callback) {
    case 'product_created':
      action = product.send
      requestCopy.body = body.codes
      break

    case 'memorial_created':
      action = memorial.send
      requestCopy.body = body
      break

    case 'account_created':
      action = account.send
      requestCopy.body = body
      break

    default:
      response.status(404).end()
      return
  }

  sendResponse(requestCopy, response, action)
}))
