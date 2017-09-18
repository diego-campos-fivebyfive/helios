'use strict'

const { exec } = require('child_process')

const postHook = (request, response) => {
  response
    .status(200)
    .json({ message: 'Hook Recieved' })
    .end()

  if (request.body.ref.includes('master')) {
    exec('$CLI_PATH/ces-app-deploy --$CES_AMBIENCE')
  }
}

module.exports = {
  post: postHook
}
