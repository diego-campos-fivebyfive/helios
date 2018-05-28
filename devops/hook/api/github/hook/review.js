'use strict'

const { exec } = require('child_process')
const { util } = require('../../../widgets')

const { pipe } = util

const PATH = '$SICES_PATH/devops/hook/api/github/hook/bin/review'

const formatArgs = args =>
  Object.values(args)
    .reduce((acc, arg) => `${acc};${arg}`)

const params = ({ pull_request, requested_reviewer }) => ({
  args: formatArgs({
    title: pull_request.title,
    developer: pull_request.user.login,
    reviewer: requested_reviewer.login
  })
})

const bin = ({ args }) =>
  exec(`${PATH} --require \'${args}\'`)

const reviewRequire = body => pipe(
  params,
  bin
)(body)

module.exports = {
  require: reviewRequire
}
