#!/bin/bash

# convert long options to short
for arg in "$@"; do
  shift
  case "$arg" in
    "--help") set -- "$@" "-h" ;;
    "--recreate") set -- "$@" "-r" ;;
    *) set -- "$@" "$arg"
  esac
done

display_usage()
{
   echo "Start application containers"
   echo
   echo "Options:"
   echo "-h|--help      Print this help"
   echo "-r|--recreate  Recreate containers"
   echo
}

while getopts "hr" option; do
    case "${option}"
        in
            r) RECREATE=true;;
            h) display_usage
               exit 0;;
    esac
done

# load configuration
. ./.env

export DOCKER_COMPOSE_FILE="docker-compose.yml"
export DOCKER_COMPOSE_LOCAL_FILE="docker-compose.local.yml"
export TARGET=base


if [[ ! -f $DOCKER_COMPOSE_FILE ]]; then
    echo "Could not find ${DOCKER_COMPOSE_FILE} configuration file"
fi

if [[ ! -f $DOCKER_COMPOSE_LOCAL_FILE ]]; then
    echo "Could not find ${DOCKER_COMPOSE_LOCAL_FILE} configuration file"
fi

if [[ ${RECREATE:-false} = true ]]; then
    # Stopping already running containers
    docker-compose -f $DOCKER_COMPOSE_FILE -f $DOCKER_COMPOSE_LOCAL_FILE down

    printf "\n"

    # Building images
    docker-compose -f $DOCKER_COMPOSE_FILE -f $DOCKER_COMPOSE_LOCAL_FILE \
        build \
        --force-rm \
        --build-arg ENV=$APP_ENV \
        --build-arg XDEBUG_REMOTE_HOST=$XDEBUG_REMOTE_HOST \
        --build-arg XDEBUG_SERVER_NAME=$XDEBUG_SERVER_NAME
fi

EXIT_CODE=$?

if [[ ${EXIT_CODE} -eq 0 ]]; then
    printf "\n"

    # Starting containers
    docker-compose -f $DOCKER_COMPOSE_FILE -f $DOCKER_COMPOSE_LOCAL_FILE \
        up \
        --no-build \
        --detach

    EXIT_CODE=$?
fi

printf "\n"

if [[ ${EXIT_CODE} -eq 0 ]]; then
    printf "\n"
    echo "App running on: http://localhost:${WEB_CONTAINER_HTTP_PORT}, https://localhost:${WEB_CONTAINER_HTTPS_PORT}"
    printf "\n"
fi

exit ${EXIT_CODE}
