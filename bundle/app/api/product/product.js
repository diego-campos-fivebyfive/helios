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

const product = ({ object }) => {
  object.forEach((product) => {
    const item = Isquik.getProduct(product)
    item.then((data) => splitProduct(data))
  })
}

module.exports = product
