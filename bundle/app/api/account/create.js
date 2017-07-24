'use strict'

const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const sendUser = account => Sices.sendUser({
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
  .then(status => (
    (status !== 304) ? 'Can not create User' : 'User and Account created successfully'
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
  .then(status => {
    if (status !== 304) {
      return 'Can not create Account'
    }
    sendUser(account)
  })

const create = ({ object }) => Isquik.getAccount(object.id).then(sendAccount)

module.exports = {
  create
}
