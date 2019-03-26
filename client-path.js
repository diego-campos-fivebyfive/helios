module.exports = {
  build: () =>
    `${process.env.SICES_PATH}/backend/web/app/${process.env.CLIENT}`,
  repo: (path = '/') =>
    `${process.env.SICES_PATH}/web-${process.env.CLIENT}${path}`
}
