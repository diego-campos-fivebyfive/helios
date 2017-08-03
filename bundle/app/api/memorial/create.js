'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const sendMemorial = ({ Dados }) => Sices.sendMemorial({
  version: Dados.Versao,
  status: Dados.Status,
  start_at: new Date(),
  end_at: new Date(),
  range: Dados.Produtos.map(x => ({
    code: x.Codigo,
    description: '',
    family: '',
    markups: x.Faixas.map(y => ({
      code: y.Id,
      initial: y.De,
      final: y.Ate,
      markup: y.Niveis.map(z => ({
        price: z.PrecoVenda,
        description: z.Descricao
      }))
    }))
  }))
})

const create = ({ object }) => Isquik.getMemorial(object.id).then(sendMemorial)

module.exports = {
  create
}
