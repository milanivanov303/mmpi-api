FROM php:7.3.6-apache

RUN apt-get update

# Install dependencies
RUN apt-get -y install openssh-server libzip-dev zip git cppcheck

# Install php mysql pdo driver
RUN docker-php-ext-install pdo_mysql

# Install php zip
RUN docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install zip

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
    && docker-php-ext-enable xdebug

# Copy vhosts
COPY vhost.conf /etc/apache2/sites-available/000-default.conf

# Copy certificates
COPY ssl/server.crt /etc/apache2/ssl/server.crt
COPY ssl/server.key /etc/apache2/ssl/server.key

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint

# Give rights to entrypoint script
RUN chmod u+x /usr/local/bin/entrypoint

# Enable apache modules
RUN a2enmod rewrite \
    && a2enmod ssl

# Run entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint"]

# Create a group and user
RUN useradd --system --groups www-data --create-home --home-dir /home/enterprise enterprise
RUN echo 'enterprise:devenv' | chpasswd
