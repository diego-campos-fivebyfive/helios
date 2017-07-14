'use strict'

const sendResponse = (request, response, action) => {
  const params = {
    id: request.params.id,
    query: request.query,
    object: request.body
  }
  action(params)
}

module.exports = sendResponse
