'use strict'

const pipe = (...functions) => x => functions.reduce((y, f) => f(y), x)

module.exports = {
  pipe
}
