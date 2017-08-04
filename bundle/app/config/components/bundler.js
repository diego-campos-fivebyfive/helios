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
    object: request.body,
    notification: request.notification
  }

  action(params)
    .then(({ statusCode, ...data }) => {
      response
        .status(statusCode)
        .json(data)
        .end()
    })
    .catch(error => {
      response
        .status(500)
        .send(`Internal Error, contact us. ${new Date()}`)
        .end()
      throw new Error(`Internal error: ${error.message}`)
    })
}

module.exports = bundler
