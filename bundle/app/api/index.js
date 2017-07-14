'use strict'
const { app } = require('../config')
const { router, sendResponse } = app
const { getNotificationData } = require('../models/isquik')

const product = require('./product')
const memorial = require('./product')

const getTypeAction = (type) => {
  switch (type) {
    case 'product': return product.list
    case 'memorial': return memorial.list
  }
}

router.post('/api/v1/notification/:id', ((request, response) => {
  const params = {
    id: request.params.id,
    type: request.body.callback
  }
  const requestCopy = request
  requestCopy.body = getNotificationData(params.id)
  const action = getTypeAction(params.type)
  sendResponse(requestCopy, response, action)
}))
