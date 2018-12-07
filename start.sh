#!/bin/bash

# load configuration
. ./.env

if [ "$1" == "--help" ]
then
    echo "Start the application using Docker

SCRIPT PARAMETERS

    N/A

ENV CONFIGS

NGINX config dynamically loaded from ./docker/ndinx/default.conf
PHP config dynamically loaded from   ./docker/php/php.ini
MySQL config dynamically loaded from ./docker/mysql/my.cnf

"
    exit
fi

if [ -f docker-compose.yml ]
then
    echo "Stopping already running containers"
    docker-compose down

    printf "\n"

    echo "Starting containers ..."
    docker-compose up --build -d
    EXIT_CODE=$?

    printf "\n"

    if [ $EXIT_CODE == 0 ]; then
        printf "\n"
        echo "App running on port: $WEB_SERVER_PORT"
    fi
fi
