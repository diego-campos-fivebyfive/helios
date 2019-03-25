const clientPath = (dir = '/') =>
  `${process.env.SICES_PATH}/web-${process.env.CLIENT}${dir}`

module.exports = clientPath
