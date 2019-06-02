FROM php:7.3.6-apache-stretch

RUN apt-get update

# Install git
RUN apt-get -y install git

# Install php mysql pdo driver
RUN docker-php-ext-install pdo_mysql

# Install ldap
RUN apt-get -y install libldap2-dev \
    && docker-php-ext-install ldap \
    && echo "TLS_REQCERT never" >> /etc/ldap/ldap.conf

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Install xdebug
RUN apt-get -y install $PHPIZE_DEPS \
    && pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini

COPY vhost.conf /etc/apache2/sites-available/000-default.conf
COPY ssl/server.crt /etc/apache2/ssl/server.crt
COPY ssl/server.key /etc/apache2/ssl/server.key
COPY start.sh /usr/local/bin/start

RUN chown -R www-data:www-data /var/www/html \
    && chmod u+x /usr/local/bin/start \
    && a2enmod rewrite \
    && a2enmod ssl

CMD ["/usr/local/bin/start"]
