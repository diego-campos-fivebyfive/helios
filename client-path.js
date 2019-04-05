const path = require('path')

const { SICES_PATH, CLIENT } = process.env

module.exports = {
  build: () =>
    path.resolve(`${SICES_PATH}/backend/web/app/${CLIENT}`),
  repo: (dir = '/') =>
    path.resolve(`${SICES_PATH}/web-${CLIENT}`, dir)
}
