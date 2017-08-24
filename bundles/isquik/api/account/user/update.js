'use strict'

const Sices = require('../../../models/sices')
const Isquik = require('../../../models/isquik')

const sendUser = ({ Dados: user }, sicesUser) =>
  Sices
    .updateUser(user.IdContatoSicesSolar, {
      ...sicesUser,
      email: user.Email,
      phone: user.Telefone,
      contact: user.Nome
    })

const updateUser = sicesUser =>
  Isquik
    .getUser(sicesUser.isquik_id)
    .then(isquikUser =>
      sendUser(isquikUser, sicesUser))

module.exports = {
  update: updateUser
}
