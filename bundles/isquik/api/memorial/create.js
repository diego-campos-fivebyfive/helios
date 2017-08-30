'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const levels = {
  BLACK: 'black',
  PLATINUM: 'platinum',
  PREMIUM: 'premium',
  PARCEIRO: 'partner',
  PROMOCIONAL: 'promotional'
}

const services = {
  INVERSOR: Sices.updateInverter,
  MODULO: Sices.updateModule,
  ESTRUTURA: Sices.updateStructure,
  STRINGBOX: Sices.updateStringbox,
  CABOS: Sices.updateVariety,
  CONECTORES: Sices.updateVariety
}

const getLevel = type => levels[type]
const getService = family => services[family]

const splitMemorial = ({ Dados: memorial }) => ({
  version: memorial.Versao,
  status: memorial.FlagPublicado,
  ranges: memorial.Produtos.map(range => ({
    code: range.Codigo,
    family: range.Grupo,
    promotional: range.FlagPromocional,
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

const updatePromotional = memorial => {
  memorial.ranges.forEach(({ code, family, promotional }) => {
    getService(family)(code, { promotional })
  })
  return memorial
}

const createMemorial = ({ notification }) =>
  Isquik
    .getMemorial(notification.id)
    .then(splitMemorial)
    .then(updatePromotional)
    .then(Sices.sendMemorial)

module.exports = {
  create: createMemorial
}
