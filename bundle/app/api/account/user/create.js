'use strict'

const Sices = require('../../../models/sices')
const Isquik = require('../../../models/isquik')

const responseIDs = sicesAccount => ({ isquikUser, sicesUser }) => ({
  Sices: {
    Conta: sicesAccount.id,
    Usuario: sicesUser.id
  },
  Isquik: {
    Integrador: sicesAccount.isquik_id,
    Contato: isquikUser.Id
  },
  statusCode: sicesUser.statusCode
})

const sendUser = sicesUser => ({ Dados: isquikUser }) =>
  Sices
    .sendUser({
      ...sicesUser,
      contact: isquikUser.Nome,
      email: isquikUser.Email,
      phone: isquikUser.Telefone
    })
    .then(data => ({
      sicesUser: data,
      isquikUser
    }))

const createUser = ({ user, account }) =>
  Isquik
    .getUser(user.isquik_id)
    .then(sendUser(user))
    .then(responseIDs(account))

module.exports = {
  create: createUser
}
