FROM mysql:8.0.13

# Copy config file
COPY mysql.cnf /etc/mysql/conf.d/mysql.cnf

# Set timezone
ARG timezone
RUN ln -snf /usr/share/zoneinfo/${timezone} /etc/localtime && echo ${timezone} > /etc/timezone
