#!/bin/bash

if [[ $EUID -eq 0 ]]; then
  echo "This script must NOT be run as root" 1>&2
  exit 1
fi
PARENT_PATH=$( cd "$(dirname "${BASH_SOURCE[0]}")" || exit ; pwd -P )
PARENT_DIR="$(basename "$PARENT_PATH")"
PUBLIC_DIR=${PARENT_PATH}/public/
RANDOM_NAME=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 100).php
touch "${PUBLIC_DIR}""${RANDOM_NAME}"
echo "<?php opcache_reset(); echo 'OPcache reset!' . PHP_EOL; ?>" | tee "${PUBLIC_DIR}""${RANDOM_NAME}" > /dev/null
docker cp "${PUBLIC_DIR}""${RANDOM_NAME}" smuldieet:/var/www/html/public/"${RANDOM_NAME}"

if [[ $PARENT_DIR = "staging.smuldieet.nl" ]]; then
  curl https://staging.smuldieet.nl/"${RANDOM_NAME}"
elif [[ $PARENT_DIR = "smuldieet.nl" ]]; then
  curl https://smuldieet.nl/"${RANDOM_NAME}"
elif [[ $PARENT_DIR = "smuldieet" ]]; then
  curl http://smuldieet/"${RANDOM_NAME}"
fi
rm "${PUBLIC_DIR}""${RANDOM_NAME}"
docker exec -it smuldieet rm /var/www/html/public/"${RANDOM_NAME}"
