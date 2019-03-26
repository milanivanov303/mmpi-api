#!/bin/bash

DOCKER_BIN="docker exec -u enterprise mmpi-api_web_1 bash -c 'source /enterprise/.custom_profile && command'"

command=${DOCKER_BIN/command/$@}

eval $command
