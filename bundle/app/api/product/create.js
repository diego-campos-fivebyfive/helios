'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')
const { util } = require('../../components')

const { pipe } = util

const splitModel = product => ({
  code: product.Codigo,
  model: product.Descricao
})

const splitDescription = product => ({
  code: product.Codigo,
  description: product.Descricao
})

const components = {
  INVERSOR: {
    split: splitModel,
    send: Sices.sendInverter
  },
  MODULO: {
    split: splitModel,
    send: Sices.sendModule
  },
  ESTRUTURA: {
    split: splitDescription,
    send: Sices.sendStructure
  },
  STRINGBOX: {
    split: splitDescription,
    send: Sices.sendStringbox
  },
  VARIEDADE: {
    split: splitDescription,
    send: Sices.sendVariety
  }
}

const getFamilyComponent = family => components[family]

const getComponent = ({ Dados: product }) => ({
  component: getFamilyComponent(product.DescricaoGrupo),
  product
})

const sendComponent = ({ component, product }) =>
  pipe(
    component.split,
    component.send
  )(product)

const sendProduct = id =>
  Isquik
    .getProduct(id)
    .then(getComponent)
    .then(sendComponent)

const getStatus = (y, component) =>
  new Promise((resolve, reject) => {
    component
      .then(x => ((x.statusCode === 201) ? resolve(x) : reject(x)))
  })

const create = ({ notification }) =>
  notification.ids
    .map(sendProduct)
    .reduce(getStatus)

module.exports = {
  create
}
