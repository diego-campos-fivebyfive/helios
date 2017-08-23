'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const splitOrder = ({ Dado: order }) => ({
  account_id: order.Integrador.IdSicesSolar,
  isquik_id: order.IdOrcamentoVenda,
  description: '',
  note: '',
  status: order.Status.IdStatusOrcamentoVenda,
  items: order.itens.map(item => ({
    description: item.DescricaoSistema,
    note: '',
    code: item.CodigoProduto,
    status: order.Status.IdStatusOrcamentoVenda,
    products: item.subItens.map(product => ({
      code: product.CodigoProduto,
      description: product.Descricao,
      quantity: product.Quantidade,
      unit_price: product.ValorUnitario,
      tag: ''
    }))
  }))
})

const joinOrders = ({ items, ...master }) => ([
  master,
  ...items
])

const sendOrders = orders =>
  orders.reduce((master, current) => {
    if (!master) {
      return Sices.sendOrder(current)
    }

    master.then(data => {
      Sices.sendOrder({
        parent_id: data.id,
        ...current
      })
    })
    return master
  }, null)

const createOrder = ({ notification }) =>
  Isquik
    .getOrder(notification.id)
    .then(splitOrder)
    .then(joinOrders)
    .then(sendOrders)

module.exports = {
  create: createOrder
}
