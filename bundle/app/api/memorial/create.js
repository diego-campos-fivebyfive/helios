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

const sendMemorial = ({ Dados }) => {

  const memorial = Sices.sendMemorial({
    version: Dados.Versao,
    status: (Dados.Status === 'Publicado') ? 1 : 0,
    start_at: '2017-08-11',
    end_at: '2017-08-11',
    range: Dados.Produtos.map(x => ({
      code: x.Codigo,
      markups: x.Faixas.map(y => ({
        initial: y.De,
        final: y.Ate,
        levels: y.Niveis.map(z => ({
          price: z.PrecoVenda,
          markup: 1.0,
          level: getLevel(z.Descricao)
        }))
      }))
    }))
  })
  memorial.then(console.log)
  return memorial
}

const create = ({ object }) => Isquik.getMemorial(object.id).then(sendMemorial)

module.exports = {
  create
}
