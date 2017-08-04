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

router.post('/api/v1/notifications', (({ body, ...request }, response) => {
  bundler({ ...request, notification: body.body }, response, event[body.callback])
}))

router.get('/', ((request, response) => {
  response.send('API Documentation')
}))
