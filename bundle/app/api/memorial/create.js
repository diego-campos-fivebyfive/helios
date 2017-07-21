'use strict'
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const send = ({ object }) => new Promise((resolve, reject) => {
  Isquik.getMemorial(object.id).then((memorial) => {
    Sices.sendMemorial({
      version: memorial.version,
      status: memorial.status,
      start_at: memorial.start_at,
      end_at: memorial.end_at,
      range: memorial.products
    })
    .then(resolve)
    .catch(reject)
  })
})

module.exports = {
  send
}
