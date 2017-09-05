'use strict'

const { router, bundler } = require('../components')
const { exec } = require('child_process')

const account = require('./account')
const memorial = require('./memorial')
const product = require('./product')
const order = require('./order')

const actions = {
  memorial_created: memorial.create,
  product_created: product.create,
  account_created: account.create,
  account_updated: account.update,
  order_created: order.create,
  order_updated: order.update
}

router.post('/api/v1/notifications', bundler(actions))
router.post('/hooks/bitbucket', ((request, response) => {
  response
    .status(200)
    .json({ message: 'Hook Recieved' })
    .end()
  exec('$CLI_PATH/ces-app-deploy --homolog')
}))
router.get('/', (request, response) => response.send('API Documentation'))
