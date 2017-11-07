'use strict'

const { deploy } = require('./deploy')
const { submit } = require('./submit')

const hook = (request, response) => {
  if (!request.headers['user-agent'].includes('GitHub-Hookshot')) {
    return
  }

  response
    .status(200)
    .json({ message: 'Hook Recieved' })
    .end()

  if (request.body.ref && request.body.ref.includes('master')) {
    deploy(request.body)
    return
  }

  if (request.body.action === 'submitted') {
    submit(request.body)
  }
}

module.exports = {
  hook
}
