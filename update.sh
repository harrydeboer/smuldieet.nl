#!/bin/bash
set -e

if [[ $EUID -eq 0 ]]; then
  echo "This script must NOT be run as root" 1>&2
  exit 1
fi
PREVIOUS=$(git rev-parse HEAD)
git pull origin master
test "$PREVIOUS" == "$(git rev-parse HEAD)" && exit 1
./installAndClearCaches.sh
