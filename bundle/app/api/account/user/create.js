'use strict'

const Sices = require('../../../models/sices')
const Isquik = require('../../../models/isquik')

const responseIDs = (sicesAccount, sicesUser, isquikUser) => ({
  Sices: {
    Conta: sicesAccount.id,
    Usuario: sicesUser.id
  },
  Isquik: {
    Integrador: sicesAccount.isquik_id,
    Contato: isquikUser.Id
  }
})

const sendUser = ({ Dados: isquikUser }, sicesUser) =>
  Sices
    .sendUser({
      ...sicesUser,
      contact: isquikUser.Nome
      email: isquikUser.Email,
      phone: isquikUser.Telefone
    })

const createUser = data =>
  Isquik
    .getUser(data.user.isquik_id)
    .then(isquikUser =>
      sendUser(isquikUser, data.user)
        .then(sicesUser =>
          responseIDs(data.account, sicesUser, isquikUser)))

module.exports = {
  create: createUser
}
