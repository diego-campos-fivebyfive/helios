SHELL := /bin/bash
PATH := ../.bin/:$(PATH)

@start_dev:
	clear; \
	echo -e "\033[93m" + "Starting server..."; \
	webpack-dev-server --hot --config .webpack.dev.conf.js; \

start_sices:
	PLATFORM=web CLIENT=sices \
    make @start_dev

build_web_sices:
	PLATFORM=web CLIENT=sices \
    node .webpack.prod.conf.js

start_integrador:
	PLATFORM=web CLIENT=integrador \
    make @start_dev

start_integrador_android:
	PLATFORM=android CLIENT=integrador \
    make @start_dev

build_web_integrador:
	PLATFORM=web CLIENT=integrador \
    node .webpack.prod.conf.js

build_mobile_web_integrador:
	PLATFORM=android CLIENT=integrador \
    node .webpack.android.conf

emulate_android_integrador:
	make build_mobile_web_integrador; \
    $$(cordova run android)

mobile_ambience_install:
	clear; \
  echo -e "\033[93m" + "Installing platforms and plugins of cordova lib..."; \
  cordova prepare --verbose

lint_template:
	pug-lint-vue src

lint_style:
	stylelint '**/*.vue' --syntax scss

lint_script:
	eslint --ext .js,.vue src

lint: | lint_template lint_style lint_script

test:
	jest src node_module/helios --config .jestrc.js
