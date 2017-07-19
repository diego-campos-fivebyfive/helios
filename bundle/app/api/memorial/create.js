'use strict'
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')
const Google = require('../../models/google')

const splitMemorial = (object) => {
  const memorial = {
    version: object.version,
    status: object.status,
    start_at: object.start_at,
    end_at: object.end_at,
    range: object.products
  }
  return Sices.sendMemorial(memorial)
}

const send = ({ object }) => {
  const auth = Google.getAuthentication()
  console.log(auth)
  return
  const memorial = Isquik.getMemorial(object.id)
  return memorial.then((data) => new Promise((resolve) => {
    splitMemorial(data)
    resolve(200)
  }))
}

module.exports = {
  send
}
