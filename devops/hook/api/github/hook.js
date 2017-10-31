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
    const { message: description } = request.body.head_commit
    const [ issue ] = description.split(/issue-/).reverse()

    const message = `[issue-${issue}] successfully merged to homolog`

    exec(`$CLI_PATH/ces-issue-move \'${issue}\' --testing`)
    exec('$CLI_PATH/ces-app-deploy --$CES_AMBIENCE')
    exec(`$CLI_PATH/ces-slack-notify --devops \'${message}\'`)

    return
  }

  if (request.body.action === 'submitted') {
    const { title, number, html_url: url } = request.body.pull_request
    const { login: developer } = request.body.pull_request.user
    const { login: reviewer } = request.body.review.user
    const { state } = request.body.review

    const action = (state === 'changes_requested') ? '`requested changes in`' : state

    const link = `*<${url}|#${number}>*`
    const message = `[${title}] @${developer}: _${reviewer}_ ${action} pull-request ${link}`

    exec(`$CLI_PATH/ces-slack-notify --devops \'${message}\'`)
  }
}

module.exports = {
  hook
}
