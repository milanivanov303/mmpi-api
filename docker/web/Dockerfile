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

# Add in .custom_profile
RUN echo $'\n\
#=================  cppcheck  ===============#\n\
export CPPCHECKVERSION="cppcheck-1.80"\n\
[[ -z ${CPPCHECKVERSION} ]] && CPPCHECK_HOME="" || CPPCHECK_HOME=${HOME}/services/built/${CPPCHECKVERSION}\n\
 if [[ -d ${CPPCHECK_HOME} ]]\n\
  then\n\
   export CPPCHECK_HOME\n\
   export CPPCHECK_BIN=${CPPCHECK_HOME}/bin\n\
 fi\n\
[[ -d ${CPPCHECK_BIN} ]] && export PATH=${CPPCHECK_BIN}:${PATH};\n\
#===========================================#\n\
' >> ${HOME}/.custom_profile

USER root
