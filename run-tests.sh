#!/bin/bash
if [[ ${OSTYPE} == 'msys' ]]; then
  php bin/console --env=test doctrine:migrations:migrate --no-interaction
  php bin/console --env=test doctrine:fixtures:load --no-interaction
  php bin/phpunit  --configuration phpunit.xml.dist
else
  docker exec -t --user=www-data smuldieet php bin/console --env=test doctrine:migrations:migrate --no-interaction
  docker exec -t --user=www-data smuldieet php bin/console --env=test doctrine:fixtures:load --no-interaction
  docker exec -t --user=www-data smuldieet php bin/phpunit --configuration phpunit.xml.dist
fi
