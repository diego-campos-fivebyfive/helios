'use strict'
const express = require('express')
const request = require('request-promise')
const notifications = require('./mocks/notifications')
const products = require('./mocks/products')
const memorial = require('./mocks/memorial')

const app = express()
app.listen(process.env.TEST_BUNDLE_PORT || 2020)

const sendNotifications = (req, res, notification) => {
  const options = {
    method: 'POST',
    uri: 'http://localhost:3000/api/v1/notifications',
    body: notification,
    json: true,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': '123'
    }
  }

  request(options).then(() => {
    res.status(200).send('Notification posted!').end()
  })
  .catch(error => {
    res.status(500).send(error.message).end()
  })
}

app.get('/product/:code', (req, res) => {
  const { code } = req.params
  const product = products.find(x => x.code === code)
  res.status(200).json(product)
})

app.get('/memorial/:id', (req, res) => {
  res.status(200).json(memorial)
})

app.get('/', (req, res) => {
  res.send(`
    <a href="/action/product-create">product-create</a>
    <a href="/action/memorial-create">memorial-create</a>
  `)
})

const { productCreated, memorialCreated } = notifications

app.get('/action/product-create', (req, res) => sendNotifications(req, res, productCreated))
app.get('/action/memorial-create', (req, res) => sendNotifications(req, res, memorialCreated))
