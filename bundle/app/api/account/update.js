'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const sendUser = account => Sices.sendUser({
  name: account.name,
  owner: account.owner,
  status: account.status
})
  .then(status => (
    (status !== 304) ? 'Can not update User' : 'User and Account updated successfully'
  ))

const sendAccount = account => Sices.sendAccount({
  name: account.name,
  owner: account.owner,
  status: account.status
})
  .then(status => {
    if (status !== 304) return 'Can not update Account'
    sendUser(account)
  })


const update = ({ object }) => Isquik.getAccount(object.id).then(isquikAccount => {
  Sices.getUser(isquikAccount.owner).then(sicesUser => {
    Sices.getAccount(sicesUser.account).then(sicesAccount => {
      console.log(sicesUser, sicesAccount)
    })
  })
})

module.exports = {
  update
}
