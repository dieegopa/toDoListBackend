#!/bin/sh

php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
supervisord -n -c /etc/supervisord.conf
