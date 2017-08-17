'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const level = {
  'BLACK': 'black',
  'PLATINUM': 'platinum',
  'PREMIUM': 'premium',
  'PARCEIRO OURO': 'gold',
  'PROMOCIONAL': 'promotional'
}

const getLevel = type => level[type]

const sendOrder = ({ Dado: order }) =>
  Sices
    .sendOrder({
      extraDocument: order.InscricaoEstadual,
      level: getLevel(order.NivelDesconto.Descricao),
      status: true,
      isquik_id: order.IdIntegrador
    })

const createOrder = ({ notification }) =>
  Isquik
    .getOrder(notification.id)
    .then(sendOrder)

module.exports = {
  create: createOrder
}
