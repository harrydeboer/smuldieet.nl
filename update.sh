#!/bin/bash
set -e

if [[ $EUID -eq 0 ]]; then
  echo "This script must NOT be run as root" 1>&2
  exit 1
fi
docker login --username harrydeboer
git pull origin master
docker-compose pull
docker-compose up -d
docker cp /var/www/letsencrypt smuldieet:/etc
docker cp /var/www/.ssh smuldieet:/var/www
docker cp /var/www/smuldieet.nl/.env.local smuldieet:/var/www/html/.env.local
PREFIX="docker exec -it --user=www-data smuldieet"
$PREFIX sh -c "test ! -d .git" && docker cp /var/www/smuldieet.nl/. smuldieet:/var/www/html
$PREFIX git pull origin master
$PREFIX composer install --no-dev --no-progress --prefer-dist
$PREFIX php bin/console cache:clear
docker-compose restart web
until $PREFIX php bin/console doctrine:migrations:migrate --no-interaction
do
  echo "Try again"
  sleep 1
done
docker system prune -f
