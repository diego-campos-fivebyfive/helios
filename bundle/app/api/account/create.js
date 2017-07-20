'use strict'
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const send = ({ object }) => new Promise((resolve, reject) => {
  Isquik.getAccount(object.id).then((data) => {
    Sices.sendAccount({
      name: data.name,
      owner: data.owner,
      status: data.status
    })
    .then(resolve)
    .catch(reject)
  })
})

module.exports = {
  send
}
