SHELL := /bin/bash
PATH := ../.bin/:$(PATH)

@start_dev:
	webpack-dev-server --hot --config ./config/webpack.dev.conf.js

start_sices:
	PLATFORM=web CLIENT=sices \
  make @start_dev

build_web_sices:
	PLATFORM=web CLIENT=sices \
  node ./config/webpack.prod.conf.js

start_integrador:
	PLATFORM=web CLIENT=integrador \
  make @start_dev

start_integrador_android:
	PLATFORM=android CLIENT=integrador \
  make @start_dev

build_web_integrador:
	PLATFORM=web CLIENT=integrador \
  node ./config/webpack.prod.conf.js

lint_template:
	pug-lint-vue ../../src --config ./config/puglint.config.js

lint_style:
	stylelint '../../**/*.vue' --syntax scss --config ./config/stylelint.config.js

lint_script:
	eslint --ext .js,.vue ../../src -c ./config/eslint.config.json

lint: | lint_template lint_style lint_script

test:
	jest src node_module/helios --config ./config/jest.config.js
