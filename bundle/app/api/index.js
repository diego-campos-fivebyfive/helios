'use strict'
const { app } = require('../config')
const { router, sendResponse } = app

const product = require('./product')
const memorial = require('./product')

const getAction = (callback) => {
  switch (callback) {
    case 'product_create': return product
    case 'memorial': return memorial
  }
}

router.post('/api/v1/notification', ((request, response) => {
  const { body, callback } = request.body
  const requestCopy = request
  requestCopy.body = body.codes
  const action = getAction(callback)
  sendResponse(requestCopy, response, action)
}))
