#!/bin/bash

RUN chown -R www-data:www-data /var/www

cd backend/


composer install

php artisan key:generate
php artisan migrate

php-fpm
