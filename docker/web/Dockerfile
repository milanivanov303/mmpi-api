FROM avitohol.codixfr.private:5000/web:latest

# set terminal
ENV TERM=vt100

ARG XDEBUG_CONFIG
ARG PHP_IDE_CONFIG

# Install cvs
RUN yum -y install cvs

USER enterprise

# set xdebug environment variables in user enterprise profile
RUN echo "export XDEBUG_CONFIG=\"$XDEBUG_CONFIG\"" >> ${HOME}/.profile
RUN echo "export PHP_IDE_CONFIG=\"$PHP_IDE_CONFIG\"" >> ${HOME}/.profile

# Create built directory if not exists
RUN mkdir -p ${HOME}/services/built

# Download
RUN cd ${HOME}/services/pck \
    && wget https://github.com/danmar/cppcheck/archive/1.80.tar.gz

# Move package for build
RUN cd ${HOME}/services/pck \
    && cp 1.80.tar.gz ../build/ \
    && cd ../build \
    && zcat 1.80.tar.gz | tar -xvf - \
    && rm 1.80.tar.gz

# Make
RUN cd ${HOME}/services/build \
    && cd cppcheck-1.80 \
    && make PREFIX=${HOME}/services/built/cppcheck-1.80 CFGDIR=${HOME}/services/built/cppcheck-1.80/cfg HAVE_RULES=yes \
    && make install PREFIX=${HOME}/services/built/cppcheck-1.80 CFGDIR=${HOME}/services/built/cppcheck-1.80/cfg

USER root
