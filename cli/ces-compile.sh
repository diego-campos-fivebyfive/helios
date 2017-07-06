#!/usr/bin/env bash

cd $SICES_PATH

rm -rf web/public

mkdir web/public
mkdir web/public/assets

cp -r web/assets/images web/public/assets/images
cp -r web/assets/font-awesome web/public/assets/font-awesome

php app/console assetic:dump
