'use strict'

const sendResponse = (request, response, action) => {
  const params = {
    id: request.params.id,
    query: request.query,
    object: request.body
  }

  action(params)
  .then((data, status = 200, message) => {
    if (message) response.statusMessage = message
    response.setHeader('Content-Type', 'application/json')
    response.status(status).json(data).end()
  })
  .catch((error) => {
    console.log(`error: ${error.message}`)
    response.status(500).end()
  })
}

module.exports = sendResponse
