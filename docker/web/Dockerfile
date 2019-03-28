FROM avitohol.codixfr.private:5000/web:latest
ARG XDEBUG_CONFIG
ARG PHP_IDE_CONFIG

# Set terminal
ENV TERM=vt100

# Install cvs
RUN yum -y install cvs

USER enterprise

# Set xdebug environment variables in user enterprise profile
RUN echo "export XDEBUG_CONFIG=\"$XDEBUG_CONFIG\"" >> ${HOME}/.profile
RUN echo "export PHP_IDE_CONFIG=\"$PHP_IDE_CONFIG\"" >> ${HOME}/.profile

# Create built directory if not exists
RUN mkdir -p ${HOME}/services/built

# ================ Install cppcheck ====================== #
ENV CPPCHECKVERSION=1.80

# Download
RUN cd ${HOME}/services/pck \
    && wget https://github.com/danmar/cppcheck/archive/${CPPCHECKVERSION}.tar.gz

# Move cppcheck for build
RUN cd ${HOME}/services/pck \
    && cp ${CPPCHECKVERSION}.tar.gz ../build/ \
    && cd ../build \
    && zcat ${CPPCHECKVERSION}.tar.gz | tar -xvf - \
    && rm ${CPPCHECKVERSION}.tar.gz

# Make cppcheck
RUN cd ${HOME}/services/build \
    && cd cppcheck-${CPPCHECKVERSION} \
    && make PREFIX=${HOME}/services/built/cppcheck-${CPPCHECKVERSION} CFGDIR=${HOME}/services/built/cppcheck-${CPPCHECKVERSION}/cfg HAVE_RULES=yes \
    && make install PREFIX=${HOME}/services/built/cppcheck-${CPPCHECKVERSION} CFGDIR=${HOME}/services/built/cppcheck-${CPPCHECKVERSION}/cfg

# Add cppcheck in .custom_profile
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
# ======================================================== #

USER root
