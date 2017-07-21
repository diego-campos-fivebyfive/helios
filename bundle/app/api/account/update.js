'use strict'
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const splitAccount = (object) => ({
  name: object.name,
  status: object.status,
  sices_account: object.sices_account,
  sices_user: object.sices_user
})

const send = ({ id, object }) => new Promise((resolve, reject) => {
  Isquik.getAccount(object.id).then((isquikAccount) => {
    Sices.getAccount(isquikAccount.owner).then((sicesAccount) => {
      Sices.getUser(sicesAccount.owner).then((sicesUser) => {

      })
    })
  })
}

module.exports = {
  send
}
