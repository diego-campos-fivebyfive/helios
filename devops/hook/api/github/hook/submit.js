'use strict'

const { exec } = require('child_process')
const { util } = require('../../../widgets')

const { pipe } = util

const getArgs = (...args) => args.reduce((sum, arg) => `${sum};${arg}`, '')

const getLink = (url, number) => `*<${url}|#${number}>*`

const getState = state =>
  ((state === 'changes_requested') ? '`requested changes in`' : state)

const getAction = (developer, reviewer) =>
  ((developer === reviewer) ? 'answer' : 'submitted')

const params = ({ pull_request: pull, review }) => ({
  args: getArgs({
    title: pull.title,
    developer: pull.user.login,
    reviewer: review.user.login,
    state: getState(review.state),
    link: getLink(pull.html_url, pull.number)
  }),
  action: getAction(pull.user.login, review.user.login)
})

const bin = ({ action, args }) => exec(`./bin/submit --${action} ${args}`)

const submit = body => pipe(
  params,
  bin
)(body)

module.exports = {
  submit
}
