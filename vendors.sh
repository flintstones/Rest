#!/bin/sh
cd $(dirname $0)
git submodule update --init
git submodule foreach git pull origin master
cd vendor/silex && git submodule update --init && cd ../..
