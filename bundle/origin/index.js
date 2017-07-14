'use strict'
const express = require('express')
const request = require('request-promise')
const notification = require('./mocks/notification')
const products = require('./mocks/products')

const app = express()
app.listen(process.env.SERVER_PORT)

const sendNotification = () => {
  const options = {
    method: 'POST',
    uri: `http://localhost:2002/api/v1/notification/:${notification.code}`,
    body: notification,
    json: true,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': '123'
    }
  }
  return request(options)
}

app.get('/', (req, res) => {
  sendNotification().then(() => {
    res.status(200).send('Notification posted!')
  })
  .catch((error) => {
    res.status(500).send(error.message)
  })
})

app.get('/notification/:code', (req, res) => {
  res.status(200).json(products)
})
