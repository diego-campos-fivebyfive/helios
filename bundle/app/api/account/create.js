'use strict'
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const send = ({ object }) => new Promise((resolve, reject) => {
  Isquik.getAccount(object.id).then((account) => {
    Sices.sendAccount({
      name: account.name,
      owner: account.owner,
      status: account.status
    })
    .then(resolve)
    .catch(reject)
  })
})

module.exports = {
  send
}
