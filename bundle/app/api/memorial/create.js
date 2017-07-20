'use strict'
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const splitMemorial = (object) => Sices.sendMemorial({
  version: object.version,
  status: object.status,
  start_at: object.start_at,
  end_at: object.end_at,
  range: object.products
})

const send = ({ object }) => {
  const memorial = Isquik.getMemorial(object.id)
  return memorial.then((data) => new Promise((resolve) => {
    splitMemorial(data)
    resolve(200)
  }))
}

module.exports = {
  send
}
