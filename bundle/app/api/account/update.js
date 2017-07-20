'use strict'
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const splitAccount = (object) => ({
  name: object.name,
  status: object.status,
  sices_account: object.sices_account,
  sices_user: object.sices_user
})

const send = ({ object }) => {
  let isquikAccount = Isquik.getAccount(object.id)
  isquikAccount = splitAccount(isquikAccount)

  const sicesAccount = Sices.getAccount(isquikAccount.sices_account)
  const sicesUsers = sicesAccount.users.map((userID) => Sices.getUser(userID))

  return account.then((data) => new Promise((resolve) => {
    resolve(200)
  }))
}

module.exports = {
  send
}
