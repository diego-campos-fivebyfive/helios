'use strict'

const express = require('express')
const http = require('http')
const socketio = require('socket.io')

const app = express()
const server = http.Server(app)

server.listen(process.env.SICES_SOCKET_PORT || 8100)

const io = socketio(server)

const room = '/socket'
const nsp = io.of(room)

nsp.on('connect', (socket) => {
  socket.memberId = socket.handshake.query.id
})

app.get('/messages', (request, response) => {
  if (!request.query.id) {
    response.json({
      error: 'undefined id'
    })
  }

  const clients = Object
    .entries(nsp.sockets)
    .forEach(([name, value]) => {
      if(request.query.id === value.memberId) {
        nsp.to(name).emit('updateTotalOfMessages', 1)
      }
    })

  response.json({
    message: 'updating client total of messages'
  })
})
