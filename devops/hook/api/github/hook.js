'use strict'

const { exec } = require('child_process')

const hook = (request, response) => {
  if (!request.headers['user-agent'].includes('GitHub-Hookshot')) {
    return
  }

  response
    .status(200)
    .json({ message: 'Hook Recieved' })
    .end()

  if (request.body.ref && request.body.ref.includes('master')) {
    exec('$CLI_PATH/ces-app-deploy --$CES_AMBIENCE')
  }

  if (request.body.action === 'submitted') {
    const { title, number, html_url: url } = request.body.pull_request
    const { login: developer } = request.body.pull_request.user
    const { login: reviewer } = request.body.review.user
    const { state } = request.body.review

    const link = `*<${url}|#${number}>*`
    const message = `[${title}] @${developer}: _${reviewer}_ ${state} pull-request ${link}`

    /*eslint-disable */
      exec(`$CLI_PATH/ces-slack-notify --devops \'${message}\'`)
    /*eslint-enable */
  }
}

module.exports = {
  hook
}
