'use strict'
//const sices = require('../../models/sices')

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

const splitData = (data) => {
  data.map((product) => {
    switch (product.family) {
      case 'module': return splitModule(product)
      case 'inverter': return splitInveter(product)
      case 'structure': return splitStructure(product)
    }
  })
}

const product = ({ object }) => object.then((data) => splitData(data))

module.exports = product
