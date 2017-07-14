'use strict'

const hasIdInjection = (id) => !Number(id)

const preRequest = (request, response, next) => {
  const id = request.params.id
  if (id && hasIdInjection(id)) {
    response.statusMessage = 'URL Invalida'
    response.status(403).end()
    return
  }
  next()
}

module.exports = preRequest
