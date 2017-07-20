'use strict'
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')


const send = ({ object }) => Isquik.getAccount(object.id).then((data) => {
  const account = {
    name: data.name,
    status: data.status,
    sices_account: data.sices_account,
    sices_user: data.sices_user
  }
  return new Promise((resolve, reject) => Sices.sendAccount(account).then(resolve).catch(reject))
})

module.exports = {
  send
}
