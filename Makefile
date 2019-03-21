SHELL := /bin/bash
PATH := ../.bin/:$(PATH)

@start_dev:
	webpack-dev-server --hot --config .webpack.dev.conf.js

@start_dev_mobile:
	webpack-dev-server --hot --config .webpack.mobile.dev.conf.js

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
    make @start_dev_mobile

start_integrador_ios:
	PLATFORM=ios CLIENT=integrador \
    make @start_dev_mobile

build_web_integrador:
	PLATFORM=web CLIENT=integrador \
	node .webpack.prod.conf.js

build_android_integrador:
	PLATFORM=android CLIENT=integrador \
    node .webpack.mobile.prod.conf

build_ios_integrador:
	PLATFORM=ios CLIENT=integrador \
    node .webpack.mobile.prod.conf

emulate_android_integrador:
	cp ./cordova/config/dev.xml config.xml -f && \
	PLATFORM=android CLIENT=integrador \
	node .webpack.mobile.dev.conf && \
    cordova run android --verbose

create_ipa_ios_file:
	zip -0 -y -r app-release.ipa platforms/ios/build/emulator && \
	echo "app-release.ipa created!"

create_installer_android_integrador:
	cp ./cordova/config/prod.xml config.xml -f && \
	make build_android_integrador && \
    cordova build android --release && \
	echo "app-release.apk created!"

create_installer_ios_integrador:
	cp ./cordova/config/prod.xml config.xml && \
	make build_ios_integrador && \
    cordova build ios --release --buildFlag='-UseModernBuildSystem=0' && \
	make create_ipa_ios_file

release_ios_integrador::
	CES_AMBIENCE=production \
	make create_installer_ios_integrador

release_android_integrador::
	CES_AMBIENCE=production \
	make create_installer_android_integrador

lint_template:
	pug-lint-vue src

lint_style:
	stylelint '**/*.vue' --syntax scss

lint_script:
	eslint --ext .js,.vue src

lint: | lint_template lint_style lint_script

test:
	jest src node_module/helios --config .jestrc.js
