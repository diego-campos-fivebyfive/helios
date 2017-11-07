'use strict'

const { exec } = require('child_process')
const { util } = require('../../../widgets')

const { pipe } = util

const getIssue = ({ content }) => content.split(/issue-/).reverse()[1]

const params = ({ head_commit }) => ({
  issue: getIssue({ content: head_commit.message })
})

const bin = ({ issue }) => exec(`./bin/deploy ${issue}`)

const deploy = body => pipe(
  params,
  bin
)(body)

module.exports = {
  deploy
}
