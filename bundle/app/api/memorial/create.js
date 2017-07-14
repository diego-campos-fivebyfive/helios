'use strict'
const Sices = require('../../models/sices')
const Isquik = require('../../models/isquik')

const splitMemorial = (object) => {
  const memorial = {
    code: object.code,
    description: object.description
  }
  return Sices.sendMemorial(memorial)
}

const send = ({ object }) => {
  return new Promise((resolve, reject) => {
    object.forEach((range) => {
      const memorial = Isquik.getMemorial(range)
      memorial.then((data) => splitMemorial(data))
    })
    resolve(200)
  })
}

module.exports = {
  send
}
