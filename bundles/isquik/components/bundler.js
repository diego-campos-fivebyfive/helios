'use strict'

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
    .catch(({ message }) => {
      response
        .status(500)
        .send(`Internal Error, contact us. ${new Date()}`)
        .end()
      throw new Error(`Internal error: ${message}`)
    })
}

module.exports = bundler