SHELL := /bin/bash
PATH := ../.bin/:$(PATH)

@start_dev:
	webpack-dev-server --hot --config .webpack.dev.conf.js \

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
	cp ./cordova/config/dev.xml config.xml -f && \
	make build_mobile_web_integrador && \
    cordova run android

release_android_integrador:
	CES_AMBIENCE=production \
	cp ./cordova/config/prod.xml config.xml -f && \
	make build_mobile_web_integrador && \
    cordova build android --release

mobile_ambience_install:
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
