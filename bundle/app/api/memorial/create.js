'use strict'
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const sendMemorial = (memorial) => Sices.sendMemorial({
  version: memorial.version,
  status: memorial.status,
  start_at: memorial.start_at,
  end_at: memorial.end_at,
  range: memorial.products
})

const create = ({ object }) => Isquik.getMemorial(object.id).then(sendMemorial)

module.exports = {
  create
}
