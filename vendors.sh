#!/bin/sh
git submodule update --init
cd vendor/silex && git submodule update --init && cd ../..
