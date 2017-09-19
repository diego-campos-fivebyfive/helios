'use strict'

const { router } = require('../widgets')

const github = require('./github')

router.post('/hooks/github', github.post)
