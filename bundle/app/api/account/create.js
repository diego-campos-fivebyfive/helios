'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')
const { create: createUser } = require('./user')

const level = {
  'BLACK': 'black',
  'PLATINUM': 'platinum',
  'PREMIUM': 'premium',
  'PARCEIRO OURO': 'gold',
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
      isquik_id: account.IdIntegrador
    })
    .then(data => createUser({
      email: account.Email,
      phone: account.Telefone,
      isquik_id: account.Administrador,
      account_id: data.id
    }))

const createAccount = ({ notification }) =>
  Isquik
    .getAccount(notification.id)
    .then(sendAccount)

module.exports = {
  create: createAccount
}
