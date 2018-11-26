SHELL := /bin/bash

build_web_sices:
	PLATFORM=web CLIENT=sices node .webpack.prod.conf.js

build_web_integrador:
	PLATFORM=web CLIENT=integrador node .webpack.prod.conf.js

start_web:
	PLATFORM=web node_modules/.bin/webpack-dev-server --hot --config .webpack.dev.conf.js

start: | start_web

lint_template:
	node_modules/.bin/pug-lint-vue src

lint_style:
	node_modules/.bin/stylelint '**/*.vue' --syntax scss

lint_script:
	node_modules/.bin/eslint --ext .js,.vue src

lint: | lint_template lint_style lint_script

test:
	node_modules/.bin/jest src node_module/helios --config .jestrc.js
