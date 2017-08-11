'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')
const { util } = require('../../components')

const { pipe } = util

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

const getComponents = () => ({
  inverter: {
    split: splitInverter,
    send: Sices.sendInverter
  },
  module: {
    split: splitModule,
    send: Sices.sendModule
  },
  structure: {
    split: splitStructure,
    send: Sices.sendStructure
  },
  stringbox: {
    split: splitStringbox,
    send: Sices.sendStringbox
  },
  variety: {
    split: splitVariety,
    send: Sices.sendVariety
  }
})

const sendProduct = ({ family, ...product }) => {
  const components = getComponents()
  const component = components[family]
  return pipe(component.split(product), component.send)
}

const create = ({ notification }) =>
  new Promise((resolve, reject) => {
    notification.codes.forEach(code =>
      Isquik
        .getProduct(code)
        .then(sendProduct)
        .then(resolve)
        .catch(reject)
    )
  })

module.exports = {
  create
}
