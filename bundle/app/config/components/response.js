'use strict'

const sendResponse = (request, response, action) => {
  const params = {
    id: request.params.id,
    query: request.query,
    object: request.body
  }
  action(params).then(status => {
    response.status(status).end()
  })
    .catch(error => {
      console.log(`error: ${error.message}`)
      response.status(500).end()
    })
}

module.exports = sendResponse
