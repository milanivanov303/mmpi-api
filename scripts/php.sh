#!/bin/bash
ARGS="$@"

# load configuration
. ./.env

docker exec -i ${PHP_CONTAINER_NAME} bash -c "php ${ARGS}"
