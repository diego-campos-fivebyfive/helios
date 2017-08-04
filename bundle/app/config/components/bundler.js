'use strict'

const bundler = (request, response, action) => {
  if (!action) {
    response
      .status(404)
      .send('Event not found')
      .end()
    return
  }

  const params = {
    id: request.params.id,
    query: request.query,
    notification: request.notification
  }

  action(params)
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
