#!/bin/bash

# load configuration
. ./.env

if [[ "$1" == "--help" ]]
then
    echo "Start the application using Docker compose

SCRIPT PARAMETERS

    N/A
"
    exit
fi

# Try to load env configuration
DOCKER_COMPOSE_FILE="docker-compose-${APP_ENV}.yml"
if [[ ! -f $DOCKER_COMPOSE_FILE ]]; then
    DOCKER_COMPOSE_FILE="docker-compose.yml"
fi

if [[ ! -f $DOCKER_COMPOSE_FILE ]]; then
    echo "Could not find docker-compose configuration file in directory"
fi

echo "Stopping already running containers"
docker-compose -f $DOCKER_COMPOSE_FILE down

printf "\n"

echo "Starting containers ..."
docker-compose -f $DOCKER_COMPOSE_FILE up --build -d
EXIT_CODE=$?

printf "\n"

if [[ ${EXIT_CODE} -eq 0 ]]; then
    printf "\n"
    echo "App running on ports: ${APACHE_CONTAINER_HTTP_PORT} and ${APACHE_CONTAINER_HTTPS_PORT}"
fi

exit ${EXIT_CODE}
