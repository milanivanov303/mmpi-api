FROM elasticsearch:7.1.1

# Set timezone
ARG timezone
RUN ln -snf /usr/share/zoneinfo/${timezone} /etc/localtime && echo ${timezone} > /etc/timezone
