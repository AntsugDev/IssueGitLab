FROM node:lts-slim AS npm-stage

WORKDIR /var/www/html
COPY ./package*.json ./
RUN npm install
COPY . .
RUN npm run build

FROM php:8.2-apache

# Install necessary dependencies
RUN apt-get update && apt-get install -y \
        build-essential \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        locales \
        zip \
        jpegoptim optipng pngquant gifsicle \
        vim unzip git curl \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        sqlite3 \
        libsqlite3-dev \
        && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd zip

WORKDIR /var/www/html

COPY . .
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY --from=npm-stage --chown=www-data:www-data /var/www/html /var/www/html

RUN composer install

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache


RUN sed -i 's|DB_DATABASE=.*|DB_DATABASE=/var/www/html/issue.sqlite|' .env

RUN a2enmod rewrite

RUN sed -i 's|AllowOverride None|AllowOverride All|' /etc/apache2/apache2.conf


RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

COPY script/entrypoint.sh /var/www/html/script/entrypoint.sh
RUN chmod +x /var/www/html/script/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/var/www/html/script/entrypoint.sh"]
CMD ["apache2-foreground"]
