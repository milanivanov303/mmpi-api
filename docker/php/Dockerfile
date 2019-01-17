FROM php:7.2-fpm-alpine

#COPY php.ini /etc/php.ini

RUN apk update

RUN apk add git

# Install mysql php pdo driver
RUN docker-php-ext-install pdo_mysql

# Install ldap
RUN apk add openldap-dev \
    && docker-php-ext-install ldap \
    && echo "TLS_REQCERT never" >> /etc/openldap/ldap.conf

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Install xdebug
RUN apk add $PHPIZE_DEPS \
    && pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=On" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=On" >> /usr/local/etc/php/conf.d/xdebug.ini

# Create a group and user
RUN addgroup -S app && adduser -S app -G app

# Tell docker that all future commands should run as the `app` user
USER app
