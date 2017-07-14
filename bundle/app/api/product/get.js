'use strict'
//const sices = require('../../models/sices')

const list = ({ object }) => {
  object.then((data) => {
    console.log(data)
  })
  return object
}

module.exports = {
  list
}
