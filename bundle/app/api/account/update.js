'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const updateUser = (sicesAccount, isquikAccount) => Sices.updateUser(sicesAccount.owner, {
  email: isquikAccount.email,
  phone: isquikAccount.phone,
  contact: isquikAccount.contact,
  account_id: sicesAccount.id
})
  .then(data => (
    (data) ? 201 : 422
  ))

const updateAccount = sicesUser => isquikAccount => Sices.updateAccount(sicesUser.account, {
  name: isquikAccount.name,
  firstname: isquikAccount.firstname,
  lastname: isquikAccount.lastname,
  email: isquikAccount.email,
  phone: isquikAccount.phone,
  document: isquikAccount.document,
  extraDocument: isquikAccount.extraDocument,
  state: isquikAccount.state,
  city: isquikAccount.city,
  contact: isquikAccount.contact,
  district: isquikAccount.district,
  street: isquikAccount.street,
  number: isquikAccount.number,
  postcode: isquikAccount.postcode,
  status: isquikAccount.status
})
  .then(sicesAccount => (
    (sicesAccount) ? updateUser(sicesAccount, isquikAccount) : 422
  ))

const update = ({ object }) => Isquik.getAccount(object.id)
  .then(isquikAccount => (
    Sices.getUser(isquikAccount.owner)
      .then(updateAccount(isquikAccount))
  ))

module.exports = {
  update
}
