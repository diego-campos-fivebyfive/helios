'use strict'

const Sices = require('../../../models/sices')
const Isquik = require('../../../models/isquik')

const sendUser = ({ Dados }, sicesUser) =>
  Sices
    .updateUser({
      ...sicesUser,
      contact: Dados.Nome
    })

const updateUser = sicesUser =>
  Isquik
    .getUser(sicesUser.isquik_id)
    .then(isquikUser =>
      sendUser(isquikUser, sicesUser))

module.exports = {
  update: updateUser
}
