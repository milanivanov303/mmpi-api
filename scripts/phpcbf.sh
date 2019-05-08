#!/bin/bash
ARGS="$@"

# load configuration
. ./.env

docker exec -i -u enterprise ${WEB_CONTAINER_NAME} bash -c ". /enterprise/.profile >/dev/null 2>&1 && ./vendor/bin/phpcbf ${ARGS}"
