'use strict'

const { app } = require('../config')

const account = require('./account')
const memorial = require('./memorial')
const product = require('./product')
const user = require('./user')

const { router, bundler } = app

const event = {
  'account_approved': account.update,
  'memorial_created': memorial.create,
  'product_created': product.create,
  '2011.1': account.create
    .then(sicesUser => user.create({ sicesUser }))
}

router.post('/api/v1/notifications', ((request, response) => {
  const {
    body: notification,
    callback: type
  } = request.body

  const requestParams = {
    ...request,
    notification
  }

  bundler(requestParams, response, event[type])
}))

router.get('/', ((request, response) => {
  response.send('API Documentation')
}))
