#!/bin/bash
set -e

if [[ $EUID -eq 0 ]]; then
  echo "This script must NOT be run as root" 1>&2
  exit 1
fi
docker-compose pull database
docker-compose pull web
docker-compose up -d
docker cp /var/www/letsencrypt smuldieet:/etc
docker cp /var/www/.ssh smuldieet:/var/www
docker cp /var/www/smuldieet.nl/.env.local smuldieet:/var/www/html/.env.local
docker-compose up -d
PREFIX="docker exec -t --user=www-data smuldieet"
$PREFIX chown www-data:www-data .env.local
$PREFIX chown -R www-data:www-data /var/www/.ssh
$PREFIX git pull origin master
$PREFIX composer install --no-dev --no-progress --prefer-dist
$PREFIX php bin/console cache:clear
./opcacheReset.sh
until $PREFIX php bin/console doctrine:migrations:migrate --no-interaction
do
  echo "Try again"
  sleep 1
done
docker system prune -f
