'use strict'
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const sendAccount = (account) => Sices.sendAccount({
  status: account.status,
  owner: account.owner,
  name: account.name
})

const create = ({ object }) => Isquik.getAccount(object.id).then(sendAccount)

module.exports = {
  create
}
