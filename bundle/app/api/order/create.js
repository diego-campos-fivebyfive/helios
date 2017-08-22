'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const getStatus = status => status === 'Aprovado'

const splitOrder = ({ Dado: order }) => ({
  account_id: order.Integrador.IdSicesSolar,
  isquik_id: order.Integrador.IdIntegrador,
  note: '',
  status: getStatus(order.Status.Descricao),
  items: order.itens.map(item => ({
    description: item.DescricaoSistema,
    note: '',
    parent_id:  order.IdSicesSolar,
    code: item.CodigoProduto,
    products: item.subItens.map(product => ({
      code: product.CodigoProduto,
      description: product.Descricao,
      quantity: product.Quantidade,
      unit_price: product.ValorUnitario,
      tag: ''
    }))
  }))
})

const joinOrders = ({ items: children, ...master }) => ([
  ...children,
  master
])

const sendOrders = orders =>
  orders.reduce((y, x) => Sices.sendOrder(x))

const createOrder = ({ notification }) =>
  Isquik
    .getOrder(notification.id)
    .then(splitOrder)
    .then(joinOrders)
    .then(sendOrders)

module.exports = {
  create: createOrder
}
