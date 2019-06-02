FROM mysql:8.0.13

COPY mysql.cnf /etc/mysql/conf.d/mysql.cnf

HEALTHCHECK --interval=5s --start-period=15s --retries=10 CMD mysqladmin ping -hlocalhost -uroot -p
