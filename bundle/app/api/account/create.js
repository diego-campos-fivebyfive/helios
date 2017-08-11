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

const sendAccount = ({ Dados }) =>
  Sices
    .sendAccount({
      document: Dados.Cnpj,
      extraDocument: Dados.InscricaoEstadual,
      firstname: Dados.RazaoSocial,
      lastname: Dados.NomeFantasia,
      postcode: Dados.Cep,
      state: Dados.UF,
      city: Dados.Cidade,
      district: Dados.Bairro,
      street: Dados.Logradouro,
      number: Dados.Numero,
      email: Dados.Email,
      phone: Dados.Telefone,
      level: level[Dados.NivelDesconto.Descricao],
      status: 1
    })
    .then(data => ({
      email: Dados.Email,
      phone: Dados.Telefone,
      isquik_id: Dados.Administrador,
      account_id: data.id
    }))

const createAccount = ({ notification }) =>
  Isquik
    .getAccount(notification.id)
    .then(sendAccount)

module.exports = {
  create: createAccount
}
