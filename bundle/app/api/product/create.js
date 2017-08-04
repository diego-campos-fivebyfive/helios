'use strict'

const { helpers } = require('../../config')
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const { pipe } = helpers

const splitModule = product => ({
  code: product.code,
  model: product.description
})

const splitInverter = product => ({
  code: product.code,
  model: product.description
})

const splitStructure = product => ({
  code: product.code,
  description: product.description
})

const splitStringbox = product => ({
    code: product.code,
    description: product.description
})

const splitVariety = product => ({
    code: product.code,
    description: product.description
})

const sendProduct = product => {
  switch (product.family) {
    case 'module':
      return pipe(splitModule(product), Sices.sendModule)
      break

    case 'inverter':
      return pipe(splitInverter(product), Sices.sendInverter)
      break

    case 'structure':
      return pipe(splitStructure(product), Sices.sendStructure)
      break

    case 'stringbox':
      return pipe(splitStringbox(product), Sices.sendStringbox)
      break

    case 'variety':
      return pipe(splitVariety(product), Sices.sendVariety)
      break

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
