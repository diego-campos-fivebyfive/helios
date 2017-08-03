'use strict'

const sendResponse = (request, response, action) => {
  const params = {
    id: request.params.id,
    query: request.query,
    notification: request.body
  }

  action(params)
    .then(({ statusCode, ...data }) => {
      response.status(statusCode).end()
    })
    .catch(error => {
      console.log(`internal error: ${error.message}`)
      response.status(500).end()
    })
}

module.exports = sendResponse
