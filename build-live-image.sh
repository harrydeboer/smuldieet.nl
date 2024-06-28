#!/bin/bash
if [[ ${OSTYPE} == 'msys' ]]; then
  PREFIX=winpty
else
  PREFIX=""
fi
$PREFIX docker login --username harrydeboer
$PREFIX docker compose -f compose.yml -f docker/compose.override_prod.yml build --no-cache
$PREFIX docker compose push
$PREFIX docker compose build --no-cache
