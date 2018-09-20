FROM mysql:5.6

#COPY my.cnf /etc/mysql/my.cnf

HEALTHCHECK --interval=5s --start-period=15s --retries=10 CMD mysqladmin ping -hlocalhost -uroot -p
