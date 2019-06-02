#!/bin/bash
ARGS="$@"

# load configuration
. ./.env

docker exec -i ${WEB_CONTAINER_NAME} bash -c "composer ${ARGS}"
