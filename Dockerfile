ARG IMAGE=gitlab.codixfr.private:5005/enterpriseapps/images/web:2.1.8-php8

# --- START BASE (needed for local development) --------------------------------- #
FROM $IMAGE-dev as base

RUN apk update \
    && apk add mercurial cppcheck freetype-dev libjpeg-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-webp --with-jpeg --with-xpm --with-freetype \
    && docker-php-ext-install -j$(nproc) gd
# --- END BASE ------------------------------------------------------------------ #


# --- START DEPENDENCIES -------------------------------------------------------- #
FROM base as dependencies

# Copy only composer files so we can use docker cache
COPY --chown=www-data:www-data composer.json composer.lock ./

# Install non dev dependencies
RUN composer install --no-progress --no-dev --no-autoloader
# --- END DEPENDENCIES ---------------------------------------------------------- #


# --- START TEST ---------------------------------------------------------------- #
FROM dependencies as test

# Install dev dependencies
RUN composer install --no-progress --no-autoloader

# Copy app code
COPY --chown=www-data:www-data . .

# Generate autoloader
RUN composer dump-autoload

# Run code sniffer check
RUN ./vendor/bin/phpcs ./App ./Modules

# Run static code analys
RUN ./vendor/bin/psalm

# Change configuration for the test
RUN cp .env.test .env || :

# Run migrations
RUN php artisan migrate

# Run tests
RUN composer test
# --- END TEST ------------------------------------------------------------------ #


# --- START BUILD --------------------------------------------------------------- #
FROM dependencies as build

# Copy app code
COPY --chown=www-data:www-data . .

# Generate autoloader
RUN composer dump-autoload

# Generate documentation
RUN php artisan openapi:generate
# --- END BUILD ----------------------------------------------------------------- #


# --- START FINAL IMAGE --------------------------------------------------------- #
FROM $IMAGE

RUN apk update \
    && apk add mercurial cppcheck freetype-dev libjpeg-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-webp --with-jpeg --with-xpm --with-freetype \
    && docker-php-ext-install -j$(nproc) gd

ENV TNS_ADMIN /var/www/html/storage/app/tns

COPY docker/php/php.ini "$PHP_INI_DIR/conf.d/"

# Copy dependencies
COPY --from=build /app .
# --- END FINAL IMAGE ----------------------------------------------------------- #
