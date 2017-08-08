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

const sendMemorial = ({ Dados }) =>
  Sices
    .sendMemorial({
      version: Dados.Versao,
      status: Dados.FlagPublicado,
      range: Dados.Produtos.map(Ranges => ({
        code: Ranges.Codigo,
        markups: Ranges.Faixas.map(Markups => ({
          initial: Markups.De,
          final: Markups.Ate,
          levels: Markups.Niveis.map(Levels => ({
            price: Levels.PrecoVenda,
            markup: 1.0,
            level: getLevel(Levels.Descricao)
          }))
        }))
      }))
    })

const create = ({ object }) =>
  Isquik
    .getMemorial(object.id)
    .then(sendMemorial)

module.exports = {
  create
}
