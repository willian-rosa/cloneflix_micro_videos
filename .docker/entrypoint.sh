#!/bin/bash

#RUN chown -R www-data:www-data /var/www

############### Front-end
RUN usermod -u 1000 www-data
npm config set cache /var/www/.npm-cache --global
cd /var/www/frontend/
npm install


############### back-end

cd /var/www/backend/
composer install
php artisan key:generate
php artisan migrate
php-fpm
