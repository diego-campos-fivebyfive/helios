'use strict'

const { router, bundler } = require('../components')

const account = require('./account')
const memorial = require('./memorial')
const product = require('./product')
const user = require('./user')

const event = {
  2011.11: account.update,
  2011.12: memorial.create,
  2011.13: product.create,
  2011.1: account.create
    .then(sicesUser => user.create({ sicesUser }))
}

router.post('/api/v1/notifications', (({ body, ...request }, response) => {
  bundler({ ...request, notification: body.body }, response, event[body.callback])
}))

router.get('/', ((request, response) => {
  response.send('API Documentation')
}))
