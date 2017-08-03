'use strict'

const { helpers } = require('../../config')
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const { pipe } = helpers

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

const sendProduct = product => {
  switch (product.family) {
    case 'module':
      return pipe(splitModule(product), Sices.sendModule)
      break

    case 'inverter':
      return pipe(splitInveter(product), Sices.sendInveter)
      break

    case 'structure':
      return pipe(splitStructure(product), Sices.sendStructure)
      break

    default:
      return new Promise((resolve, reject) => reject(404))
  }
}

const create = ({ notification }) => new Promise((resolve, reject) => {
  notification.forEach(code => Isquik.getProduct(code)
    .then(sendProduct)
    .then(resolve)
    .catch(reject)
  )
})

module.exports = {
  create
}
