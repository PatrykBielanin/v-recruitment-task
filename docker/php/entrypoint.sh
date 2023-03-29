#!/bin/bash

chown www-data /var/www/html/.env
chmod -R 777 /var/www/html

composer update --ignore-platform-reqs
php-fpm -O
