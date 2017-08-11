'use strict'

const { router, bundler } = require('../components')

const account = require('./account')
const memorial = require('./memorial')
const product = require('./product')
const user = require('./user')

const actions = {
  memorial_created: memorial.create,
  product_created: product.create,
  account_updated: account.update.then(sicesUser => user.update({ sicesUser }),
  account_created: account.create.then(sicesUser => user.create({ sicesUser }))
}

router.post('/api/v1/notifications', (({ body, ...request }, response) => {
  bundler({ ...request, notification: body.body }, response, actions[body.callback])
}))

router.get('/', ((request, response) => {
  response.send('API Documentation')
}))
