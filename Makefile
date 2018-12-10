SHELL := /bin/bash
PATH := ../.bin/:$(PATH)

@start_dev:
	webpack-dev-server --hot --config .webpack.dev.conf.js

# web-sices
start_sices:
	PLATFORM=web CLIENT=sices \
  make @start_dev --inspect

build_web_sices:
	PLATFORM=web CLIENT=sices \
  node .webpack.prod.conf.js

# web-integrador
start_integrador:
	PLATFORM=web CLIENT=integrador \
  make @start_dev

start_integrador_android:
	PLATFORM=android CLIENT=integrador \
  make @start_dev

build_web_integrador:
	PLATFORM=web CLIENT=integrador \
  node .webpack.prod.conf.js

# generals
lint_template:
	pug-lint-vue src

lint_style:
	stylelint '**/*.vue' --syntax scss

lint_script:
	eslint --ext .js,.vue src

lint: | lint_template lint_style lint_script

test:
	jest src node_module/helios --config .jestrc.js
