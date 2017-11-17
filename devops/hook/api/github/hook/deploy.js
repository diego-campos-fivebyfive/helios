'use strict'

const { exec } = require('child_process')
const { util } = require('../../../widgets')

const { pipe } = util

const PATH = '$SICES_PATH/devops/hook/api/github/hook/bin/deploy'

const getIssue = ({ content }) => content.split(/issue-/).reverse()[1]

const hasOnlyDraftModifications = ({ files }) =>
  files.reduce((acc, file) => (acc && file.includes('draft.md')), true)

const params = ({ head_commit }) => ({
  issue: getIssue({ content: head_commit.message }),
  draft: hasOnlyDraftModifications({ files: head_commit.modified })
})

const bin = ({ issue, draft }) => exec(`${PATH} ${issue} ${draft}`)

const deploy = body => pipe(
  params,
  bin
)(body)

module.exports = {
  deploy
}
