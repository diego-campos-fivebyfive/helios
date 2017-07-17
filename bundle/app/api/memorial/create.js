'use strict'
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const splitMemorial = (object) => {
  const memorial = {
    version: object.version,
    status: object.status
  }
  return Sices.sendMemorial(memorial)
}

const send = ({ object }) => {
  const memorial = Isquik.getMemorial(object.id)
  return memorial.then((data) => splitMemorial(data))
}

module.exports = {
  send
}
