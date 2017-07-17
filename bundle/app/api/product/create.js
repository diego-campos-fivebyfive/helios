'use strict'
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const splitModule = (object) => ({
  code: object.code,
  model: object.description
})

const splitInveter = (object) => ({
  code: object.code,
  model: object.description
})

const splitStructure = (object) => ({
  code: object.code,
  description: object.description
})

const splitProduct = (product) => {
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

const send = ({ object }) => {
  return new Promise((resolve, reject) => {
    object.forEach((productCode) => {
      const product = Isquik.getProduct(productCode)
      product.then((data) => splitProduct(data))
    })
    resolve(200)
  })
}

module.exports = {
  send
}
