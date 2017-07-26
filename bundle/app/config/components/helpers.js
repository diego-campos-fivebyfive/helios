'use strict'

const pipe = (...fns) => fns.reduce((y, f) => f(y))

module.exports = {
  pipe
}
