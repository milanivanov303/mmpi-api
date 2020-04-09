FROM avitohol.codixfr.private:5000/php:7.3.6-fpm-v9

# Copy config file
ARG display_errors
RUN if [ ${display_errors} -eq 1 ]; then cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"; fi

USER root
RUN apt-get -y install mercurial-server cppcheck
USER enterprise
