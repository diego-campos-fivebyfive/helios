'use strict'
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const send = ({ object }) => new Promise((resolve, reject) => {
  Isquik.getMemorial(object.id).then((data) => {
    Sices.sendMemorial({
      version: data.version,
      status: data.status,
      start_at: data.start_at,
      end_at: data.end_at,
      range: data.products
    })
    .then(resolve)
    .catch(reject)
  })
})

module.exports = {
  send
}
