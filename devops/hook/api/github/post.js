'use strict'

const { exec } = require('child_process')

const allowOrigin = (request, agent) =>
  request.headers['user-agent'].includes(agent)

const postHook = (request, response) => {
  const githubAgent = 'GitHub-Hookshot/ab46a57'
  if (!allowOrigin(request, githubAgent)) {
    return
  }

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
