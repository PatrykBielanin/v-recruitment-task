FROM php:8.1-fpm-buster

ARG PHP_CONFIG_NAME
ARG PHP_BASE_CONFIG_NAME
ARG PHP_QUEUES_COUNT
ENV PHP_QUEUES_COUNT ${PHP_QUEUES_COUNT}

RUN mv $PHP_INI_DIR/${PHP_BASE_CONFIG_NAME} $PHP_INI_DIR/php.ini

COPY $PHP_CONFIG_NAME $PHP_INI_DIR/conf.d/default.ini

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

EXPOSE 7474

RUN apt-get update && apt-get install --yes git

RUN pecl install apcu
RUN docker-php-ext-install opcache && docker-php-ext-enable opcache apcu

COPY entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

ENTRYPOINT /entrypoint.sh
