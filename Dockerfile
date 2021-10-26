ARG IMAGE=gitlab.codixfr.private:5005/enterpriseapps/images/web:2.1.8-php8

# --- START BASE (needed for local development) --------------------------------- #
FROM $IMAGE-dev as base

RUN apk update \
    && apk add mercurial cppcheck freetype-dev libjpeg-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-webp --with-jpeg --with-xpm --with-freetype \
    && docker-php-ext-install -j$(nproc) gd

# Install oci8
RUN apk --no-cache add libaio libc6-compat \
    && curl -o /tmp/instantclient-basic-linux.x64-21.3.0.0.0.zip https://download.oracle.com/otn_software/linux/instantclient/213000/instantclient-basic-linux.x64-21.3.0.0.0.zip -SL \
    && curl -o /tmp/instantclient-sdk-linux.x64-21.3.0.0.0.zip https://download.oracle.com/otn_software/linux/instantclient/213000/instantclient-sdk-linux.x64-21.3.0.0.0.zip -SL \
    && unzip /tmp/instantclient-basic-linux.x64-21.3.0.0.0.zip -d /usr/local/ \
    && unzip /tmp/instantclient-sdk-linux.x64-21.3.0.0.0.zip -d /usr/local/ \
    && ln -s /usr/local/instantclient_21_3 /usr/local/instantclient \
    && ln -s /lib/libc.so.6 /usr/lib/libresolv.so.2 \
    && ln -s /lib64/ld-linux-x86-64.so.2 /usr/lib/ld-linux-x86-64.so.2

ENV LD_LIBRARY_PATH /usr/local/instantclient/
ENV ORACLE_HOME /usr/local/instantclient/

#RUN echo 'instantclient,/usr/local/instantclient' | pecl install oci8-2.2.0
RUN docker-php-ext-configure oci8 --with-oci8=instantclient,/usr/local/instantclient \
    && docker-php-ext-install oci8 \
    && docker-php-ext-enable oci8

ENV TNS_ADMIN /app/storage/app/tns

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

# Install oci8
RUN apk --no-cache add libaio libc6-compat \
    && curl -o /tmp/instantclient-basic-linux.x64-21.3.0.0.0.zip https://download.oracle.com/otn_software/linux/instantclient/213000/instantclient-basic-linux.x64-21.3.0.0.0.zip -SL \
    && curl -o /tmp/instantclient-sdk-linux.x64-21.3.0.0.0.zip https://download.oracle.com/otn_software/linux/instantclient/213000/instantclient-sdk-linux.x64-21.3.0.0.0.zip -SL \
    && unzip /tmp/instantclient-basic-linux.x64-21.3.0.0.0.zip -d /usr/local/ \
    && unzip /tmp/instantclient-sdk-linux.x64-21.3.0.0.0.zip -d /usr/local/ \
    && ln -s /usr/local/instantclient_21_3 /usr/local/instantclient \
    && ln -s /lib/libc.so.6 /usr/lib/libresolv.so.2 \
    && ln -s /lib64/ld-linux-x86-64.so.2 /usr/lib/ld-linux-x86-64.so.2

ENV LD_LIBRARY_PATH /usr/local/instantclient/
ENV ORACLE_HOME /usr/local/instantclient/

RUN docker-php-ext-configure oci8 --with-oci8=instantclient,/usr/local/instantclient \
    && docker-php-ext-install oci8 \
    && docker-php-ext-enable oci8

ENV TNS_ADMIN /app/storage/app/tns

COPY docker/php/php.ini "$PHP_INI_DIR/conf.d/"

# Copy dependencies
COPY --from=build /app .
# --- END FINAL IMAGE ----------------------------------------------------------- #
