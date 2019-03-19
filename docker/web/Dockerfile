FROM avitohol.codixfr.private:5000/web:latest

ENV TERM=vt100

ARG XDEBUG_CONFIG
ARG PHP_IDE_CONFIG

RUN echo "export XDEBUG_CONFIG=\"$XDEBUG_CONFIG\"" >> /enterprise/.profile
RUN echo "export PHP_IDE_CONFIG=\"$PHP_IDE_CONFIG\"" >> /enterprise/.profile
