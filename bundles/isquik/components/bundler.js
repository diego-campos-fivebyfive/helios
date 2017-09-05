'use strict'

const { exec } = require('child_process')

const bundler = actions => (request, response) => {
  const { callback, body } = request.body
  const action = actions[callback]

  if (!action) {
    response
      .status(404)
      .send('Event not found')
      .end()
    return
  }

  action({
    id: request.params.id,
    query: request.query,
    notification: body
  })
    .then(({ statusCode, ...data }) => {
      response
        .status(statusCode)
        .json(data)
        .end()
    })
    .catch(message => {
      response
        .status(500)
        .send(`Internal Error, contact us. ${new Date()}`)
        .end()

      if (process.env.CES_AMBIENCE === 'development') {
        throw new Error(`Internal error: ${JSON.stringify(message)}`)
      } else {
        /*eslint-disable */
        exec(`$CLI_PATH/ces-slack-notify --backlog-api \'${JSON.stringify(message)}\'`)
        /*eslint-enable */
      }
    })
}

module.exports = bundler
