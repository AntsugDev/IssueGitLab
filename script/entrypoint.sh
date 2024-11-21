#!/bin/sh

php artisan migrate
php artisan migrate:fresh --seed

if [ -f /var/www/html/issue.sqlite ]; then
    echo "Setting permissions for issue.sqlite"
    chown www-data:www-data /var/www/html/issue.sqlite
    chmod 664 /var/www/html/issue.sqlite
fi

php artisan cache:clear
php artisan queue:work --daemon &

exec "$@"
