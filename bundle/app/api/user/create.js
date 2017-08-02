'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const sendUser = ({ Dados }, sicesUser) =>
  Sices
    .sendUser({
      ...sicesUser,
      contact: Dados.Nome
    })
    .then(data => ((data) ? 201 : 422))

const create = ({ object }) =>
  Isquik
    .getUser(object.id)
    .then(isquikUser => sendUser(isquikUser, object))


module.exports = {
  create
}
