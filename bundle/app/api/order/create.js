'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const splitOrder = ({ Dado: order }) => ({
  isquik_id: order.IdIntegrador,
  version: memorial.Versao,
  status: memorial.FlagPublicado,
  range: memorial.Produtos.map(range => ({
    code: range.Codigo,
    markups: range.Faixas.map(markup => ({
      initial: markup.De,
      final: markup.Ate,
      levels: markup.Niveis.map(level => ({
        price: level.PrecoVenda,
        level: getLevel(level.Descricao)
      }))
    }))
  }))
})

const joinOrders = ({ itens: children, ...master }) => ([
  ...children,
  master
])

const sendOrders = orders =>
  orders.reduce(Sices.sendOrder)

const createOrder = ({ notification }) =>
  Isquik
    .getOrder(notification.id)
    .then(splitOrder)
    .then(joinOrders)
    .then(sendOrders)

module.exports = {
  create: createOrder
}
