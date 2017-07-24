'use strict'
const { helpers } = require('../../config')
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const { pipe } = helpers

const splitModule = (product) => ({
  code: product.code,
  model: product.description
})

const splitInveter = (product) => ({
  code: product.code,
  model: product.description
})

const splitStructure = (product) => ({
  code: product.code,
  description: product.description
})

const sendProduct = (product) => {
  switch (product.family) {
    case 'module':
      const module = splitModule(product)
      return Sices.sendModule(module)

    case 'inverter':
      const inverter = splitInveter(product)
      return Sices.sendInveter(inverter)

    case 'structure':
      const structure = splitStructure(product)
      return Sices.sendStructure(structure)
  }
}

const create = ({ object }) => new Promise((resolve, reject) => {
  object.forEach((code) => Isquik.getProduct(code)
    .then(sendProduct)
    .then(resolve)
    .catch(reject)
  )
})

module.exports = {
  create
}
