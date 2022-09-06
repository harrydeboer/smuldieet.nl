#!/bin/bash
if [[ ${OSTYPE} == 'msys' ]]; then
  PREFIX=winpty
else
  PREFIX=""
fi
$PREFIX docker login --username harrydeboer
$PREFIX docker-compose -f docker-compose.yml -f docker/docker-compose.override_prod.yml build --no-cache
$PREFIX docker-compose push
$PREFIX docker-compose build --no-cache
