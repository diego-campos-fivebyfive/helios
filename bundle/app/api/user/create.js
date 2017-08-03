'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const sendUser = ({ Dados }, sicesUser) =>
  Sices
    .sendUser({
      ...sicesUser,
      contact: Dados.Nome
    })

const createUser = ({ notification }) =>
  Isquik
    .getUser(notification.id)
    .then(isquikUser =>
      sendUser(isquikUser, notification))


module.exports = {
  create: createUser
}
