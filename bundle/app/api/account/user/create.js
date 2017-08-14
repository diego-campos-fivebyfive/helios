'use strict'

const Sices = require('../../../models/sices')
const Isquik = require('../../../models/isquik')

const sendUser = ({ Dados: user }, sicesUser) =>
  Sices
    .sendUser({
      ...sicesUser,
      contact: user.Nome
    })

const createUser = sicesUser =>
  Isquik
    .getUser(sicesUser.isquik_id)
    .then(isquikUser =>
      sendUser(isquikUser, sicesUser))

module.exports = {
  create: createUser
}
