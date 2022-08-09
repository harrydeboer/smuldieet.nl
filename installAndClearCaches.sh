#!/bin/bash
set -e

if [[ $EUID -eq 0 ]]; then
  echo "This script must NOT be run as root" 1>&2
  exit 1
fi
./dockerBuildAndUp.sh
PREFIX="docker exec -t --user=www-data smuldieet"
$PREFIX composer install --no-dev --no-progress --prefer-dist
$PREFIX php bin/console cache:clear
./opcacheReset.sh
until $PREFIX php bin/console doctrine:migrations:migrate --no-interaction
do
  echo "Try again"
  sleep 1
done
docker system prune -f
