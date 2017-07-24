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
    case 'module': return pipe(Sices.sendModule, splitModule(product))
    case 'inverter': return pipe(Sices.sendInveter, splitInveter(product))
    case 'structure': return pipe(Sices.sendStructure, splitStructure(product))
    default: return new Promise((resolve, reject) => reject(404))
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
