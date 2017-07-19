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

app.get('/', (req, res) => {
  res.send(`
    <a href="/action/product-create">product-create</a>
    <a href="/action/memorial-create">memorial-create</a>
  `)
})

app.get('/product/:code', (req, res) => {
  const { code } = req.params
  const product = products.find(x => x.code === code)
  res.status(200).json(product)
})

app.get('/memorial/:id', (req, res) => {
  res.status(200).json(memorial)
})

const getData = (uri) => request({ method: 'GET', uri }).then((x) => JSON.parse(x))

app.post('/notifications', (req, res) => {
  const { callback, body } = req.body
  let data

  switch (callback) {
    case 'product_validate':
      data = getData(`${SICES_HOST}:${SICES_PORT}/api/${body.family}/${body.id}`)
      break

    case 'account_created':
      const accounts = getData(`${SICES_HOST}:${SICES_PORT}/api/accounts/${body.id}`)
      data = accounts.users.map((x) => getData(`${SICES_HOST}:${SICES_PORT}/api/users/${body.id}`))
      break

    default:
      res.status(404).end('callback action not found')
      return
  }

  res.status(200).json({ callback, body: data })
})

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

const { productCreated, memorialCreated } = notifications

app.get('/action/product-create', (req, res) => sendNotifications(req, res, productCreated))
app.get('/action/memorial-create', (req, res) => sendNotifications(req, res, memorialCreated))
