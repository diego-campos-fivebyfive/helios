'use strict'

const { router, bundler } = require('../components')

const account = require('./account')
const memorial = require('./memorial')
const product = require('./product')
const user = require('./user')

const actions = {
  memorial_created: memorial.create,
  product_created: product.create,
  account_created: account.create,
  account_updated: account.update
}

router.post('/api/v1/notifications', bundler(actions))
router.get('/', (request, response) => response.send('API Documentation'))
