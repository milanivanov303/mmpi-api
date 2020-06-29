FROM avitohol.codixfr.private:5000/php:7.3.6-fpm-v9

# Copy config file
ARG display_errors
RUN if [ ${display_errors} -eq 1 ]; then cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"; fi

USER root
RUN apt-get -y install mercurial-server cppcheck
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-webp --with-jpeg --with-xpm --with-freetype \
    && docker-php-ext-install -j$(nproc) gd
ENV TNS_ADMIN /var/www/html/storage/app/tns
USER enterprise
