#!/bin/sh
docker run -w=/var/www -v $PWD:/var/www -p 80:80 -ti php:7.4-apache-xdebug "$@"
