#!/bin/bash
# Install php dependencies
please composer install

# Run migrations
please php artisan migrate