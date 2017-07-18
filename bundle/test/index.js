'use strict'
const express = require('express')
const bodyParser = require('body-parser')
const request = require('request-promise')
const notifications = require('./mocks/notifications')
const products = require('./mocks/products')
const memorial = require('./mocks/memorial')

const SICES_HOST = process.env.CES_SICES_HOST
const SICES_PORT = process.env.CES_SICES_PORT
const BUNDLE_HOST = process.env.CES_BUNDLE_HOST
const BUNDLE_PORT = process.env.CES_BUNDLE_PORT
const ISQUIK_PORT = process.env.CES_ISQUIK_PORT

const app = express()
app.listen(ISQUIK_PORT)

app.use(bodyParser.json())
app.use(bodyParser.urlencoded({ extended: true }))

const sendNotifications = (req, res, notification) => {
  const options = {
    method: 'POST',
    uri: `${BUNDLE_HOST}:${BUNDLE_PORT}/api/v1/notifications`,
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

const getData = (uri) => {
  let options = {
    method: 'GET'
  }
  options = Object.assign(options, { uri })
  return request(options).then((data) => JSON.parse(data))
}

app.post('/notifications', (req, res) => {
  const { callback, body } = req.body
  let data

  switch (callback) {
    case 'product_created':
      data = getData(`${SICES_HOST}:${SICES_PORT}/${body.family}/${body.code}`)
      break

    default:
      response.status(404).end('callback action not found')
      return
  }

  res.status(200).json({ callback, body: data })
})

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

