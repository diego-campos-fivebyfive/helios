'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')
const { create: createUser } = require('./user')

const level = {
  'BLACK': 'black',
  'PLATINUM': 'platinum',
  'PREMIUM': 'premium',
  'PARCEIRO OURO': 'gold',
  'PARCEIRO': 'partner',
  'PROMOCIONAL': 'promotional'
}

const getLevel = type => level[type]

const sendAccount = ({ Dados: account }) =>
  Sices
    .sendAccount({
      document: account.Cnpj,
      extraDocument: account.InscricaoEstadual,
      firstname: account.RazaoSocial,
      lastname: account.NomeFantasia,
      postcode: account.Cep,
      state: account.UF,
      city: account.Cidade,
      district: account.Bairro,
      street: account.Logradouro,
      number: account.Numero,
      email: account.Email,
      phone: account.Telefone,
      level: getLevel(account.NivelDesconto.Descricao),
      status: true,
      confirmationToken: account.TokenLiberacaoAcesso,
      isquik_id: account.IdIntegrador
    })
    .then(data => createUser({
      account: {
        isquik_id: account.IdIntegrador,
        id: data.id
      },
      user: {
        isquik_id: account.Administrador,
        account_id: data.id
      }
    }))

const createAccount = ({ notification }) =>
  Isquik
    .getAccount(notification.id)
    .then(sendAccount)

module.exports = {
  create: createAccount
}
