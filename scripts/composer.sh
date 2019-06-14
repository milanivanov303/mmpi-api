#!/bin/bash
ARGS="$@"

# load configuration
. ./.env

docker exec -i -u enterprise ${WEB_CONTAINER_NAME} bash -c "composer ${ARGS}"
