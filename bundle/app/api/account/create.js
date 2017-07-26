'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const sendUser = (account, id) => Sices.sendUser({
  email: account.email,
  phone: account.phone,
  contact: account.contact,
  account_id: id
})
  .then(status => (
    (status !== 200) ? 'Can not create User' : 'User and Account created successfully'
  ))

const sendAccount = account => Sices.sendAccount({
  name: account.name,
  firstname: account.firstname,
  lastname: account.lastname,
  email: account.email,
  phone: account.phone,
  document: account.document,
  extraDocument: account.extraDocument,
  state: account.state,
  city: account.city,
  contact: account.contact,
  district: account.district,
  street: account.street,
  number: account.number,
  postcode: account.postcode,
  status: account.status
})
  .then((data) => {
    if (data) {
      sendUser(account, data.account_id)
      return 200
    }
    return 500
  })

const create = ({ object }) => Isquik.getAccount(object.id).then(sendAccount)

module.exports = {
  create
}
