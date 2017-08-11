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
      range: memorial.Produtos.map(ranges => ({
        code: ranges.Codigo,
        markups: ranges.Faixas.map(markups => ({
          initial: markups.De,
          final: markups.Ate,
          levels: markups.Niveis.map(levels => ({
            price: levels.PrecoVenda,
            markup: 1.0,
            level: getLevel(levels.Descricao)
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
