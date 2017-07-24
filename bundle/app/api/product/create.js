'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const splitModule = product => ({
  code: product.code,
  model: product.description
})

const splitInveter = product => ({
  code: product.code,
  model: product.description
})

const splitStructure = product => ({
  code: product.code,
  description: product.description
})

let data

const sendProduct = product => {
  switch (product.family) {
    case 'module':
      data = splitModule(product)
      return Sices.sendModule(data)

    case 'inverter':
      data = splitInveter(product)
      return Sices.sendInveter(data)

    case 'structure':
      data = splitStructure(product)
      return Sices.sendStructure(data)

    default:
      return new Promise((resolve, reject) => reject(404))
  }
}

const create = ({ object }) => new Promise((resolve, reject) => {
  object.forEach(code => Isquik.getProduct(code)
    .then(sendProduct)
    .then(resolve)
    .catch(reject)
  )
})

module.exports = {
  create
}
