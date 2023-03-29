FROM nginx:latest

ARG NGINX_CONFIG_NAME

RUN apt update && apt install build-essential zlib1g-dev libpcre3-dev unzip wget uuid-dev sudo memcached -y

COPY ${NGINX_CONFIG_NAME} /etc/nginx/templates/default.conf.template
