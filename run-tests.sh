#!/bin/bash
NUMBER_OF_CPUS=$(nproc)
if [[ ${NUMBER_OF_CPUS} -lt 4 ]]; then
  PROCESSES=$NUMBER_OF_CPUS
else
  PROCESSES=4
fi
php bin/console cache:clear --env=test
if [[ ${OSTYPE} == 'msys' ]]; then
  php ./vendor/bin/paratest -p$PROCESSES --configuration phpunit.xml
else
  docker exec -it --user=www-data smuldieet php ./vendor/bin/paratest -p$PROCESSES --configuration phpunit.xml
fi
