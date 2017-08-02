'use strict'

const { app } = require('../config')

const account = require('./account')
const memorial = require('./memorial')
const product = require('./product')
const user = require('./user')

const { router, sendResponse } = app

router.post('/api/v1/notifications', ((request, response) => {
  const { body, callback } = request.body
  const requestCopy = request
  let action

  switch (callback) {
    case '2011.1':
      account
        .create({
          object: body
        })
        .then(data => {
          action = user.create
          requestCopy.body = data
          sendResponse(requestCopy, response, action)
        })
      return

    case 'account_approved':
      action = account.update
      requestCopy.body = body
      break

    case 'memorial_created':
      action = memorial.create
      requestCopy.body = body
      break

    case 'product_created':
      action = product.create
      requestCopy.body = body.codes
      break

    default:
      response.status(404).send('callback action not found').end()
      return
  }

  sendResponse(requestCopy, response, action)
}))

router.get('/', ((request, response) => {
  response.send('API Documentation')
}))
