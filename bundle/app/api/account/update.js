'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const sendUser = account => Sices.sendUser({
  name: account.name,
  owner: account.owner,
  status: account.status
})
  .then(status => (
    (status !== 202) ? 'Can not update User' : 'User and Account updated successfully'
  ))

const sendAccount = account => Sices.sendAccount({
  name: account.name,
  owner: account.owner,
  status: account.status
})
  .then(status => {
    if (status !== 202) {
      return 'Can not update Account'
    }

    sendUser(account)
  })

const update = ({ object }) => Isquik.getAccount(object.id).then(isquikAccount => {
  console.log('is', isquikAccount)
  Sices.getUser(isquikAccount.owner).then(sicesUser => {
    console.log('us', sicesUser)
    Sices.getAccount(sicesUser.account).then(sicesAccount => {
      console.log('ac', sicesAccount)
    })
  })
})

module.exports = {
  update
}
