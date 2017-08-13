'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')
const { util } = require('../../components')

const { pipe } = util

const splitModel = product => ({
  code: product.code,
  model: product.description
})

const splitDescription = product => ({
  code: product.code,
  description: product.description
})

const components = {
  inverter: {
    split: splitModel,
    send: Sices.sendInverter
  },
  module: {
    split: splitModel,
    send: Sices.sendModule
  },
  structure: {
    split: splitDescription,
    send: Sices.sendStructure
  },
  stringbox: {
    split: splitDescription,
    send: Sices.sendStringbox
  },
  variety: {
    split: splitDescription,
    send: Sices.sendVariety
  }
}

const getComponent = ({ family, ...product }) => ({
  component: components[family],
  product
})

const sendComponent = ({ component, product }) =>
  pipe(
    component.split,
    component.send
  )(product)


const create = ({ notification }) =>
  new Promise((resolve, reject) => {
    notification.codes.forEach(code =>
      Isquik
        .getProduct(code)
        .then(getComponent)
        .then(sendComponent)
        .then(resolve)
        .catch(reject)
    )
  })

module.exports = {
  create
}
