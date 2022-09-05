#!/bin/bash
set -e

if [[ $EUID -eq 0 ]]; then
  echo "This script must NOT be run as root" 1>&2
  exit 1
fi
docker-compose -f docker-compose.yml -f docker-compose.live.yml -f docker-compose.{APP_ENV}.yml build --no-cache
docker-compose -f docker-compose.yml -f docker-compose.live.yml -f docker-compose.{APP_ENV}.yml up -d --remove-orphans
docker cp /var/www/letsencrypt smuldieet:/etc
docker-compose -f docker-compose.yml -f docker-compose.live.yml -f docker-compose.{APP_ENV}.yml up -d --remove-orphans
