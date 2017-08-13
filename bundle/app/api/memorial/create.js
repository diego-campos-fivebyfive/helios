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

const sendMemorial = ({ Dados: memorial }) =>
  Sices
    .sendMemorial({
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

const createMemorial = ({ notification }) =>
  Isquik
    .getMemorial(notification.id)
    .then(sendMemorial)

module.exports = {
  create: createMemorial
}
