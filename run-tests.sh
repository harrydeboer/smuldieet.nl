#!/bin/bash
if [[ ${OSTYPE} == 'msys' ]]; then
  php bin/console --env=test doctrine:migrations:migrate --no-interaction
  php bin/console --env=test doctrine:fixtures:load --no-interaction
  php bin/phpunit  --display-phpunit-notices --configuration phpunit.dist.xml
else
  docker exec -t --user=www-data smuldieet php bin/console --env=test doctrine:migrations:migrate --no-interaction
  docker exec -t --user=www-data smuldieet php bin/console --env=test doctrine:fixtures:load --no-interaction
  docker exec -t --user=www-data smuldieet php bin/phpunit --display-phpunit-notices --configuration phpunit.dist.xml
fi
