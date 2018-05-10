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
  socket.memberToken = socket.handshake.query.token
})

app.get('/messages', (request, response) => {
  const { token } = request.query

  if (!token) {
    response.json({
      error: 'undefined id'
    })
  }

  const clients = Object
    .entries(nsp.sockets)
    .forEach(([name, value]) => {
      if(token === value.memberToken) {
        nsp.to(name).emit('updateTotalOfMessages', 1)
      }
    })

  response.json({
    message: 'updating client total of messages'
  })
})
