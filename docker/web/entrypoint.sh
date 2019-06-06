#!/usr/bin/env bash

set -e

ROLE=${CONTAINER_ROLE:-web}

if [ ${ROLE} = "scheduler" ]; then

    echo "Running the scheduler..."
    while [ true ]
    do
        cd /var/www/html && php artisan schedule:run --verbose --no-interaction &
        sleep 60
    done

elif [ ${ROLE} = "queue" ]; then

    echo "Running the queue..."
    cd /var/www/html && php artisan queue:work --verbose --tries=3 --timeout=90

else

    echo "Running apache..."

    /etc/init.d/ssh start
    exec apache2-foreground

fi
