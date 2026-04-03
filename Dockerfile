FROM php:8.3-apache
RUN apt-get update && apt-get install -y libpq-dev libonig-dev unzip git \
    && docker-php-ext-install pdo pdo_pgsql mbstring
RUN a2enmod rewrite
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# Set the DocumentRoot directly to /var/www/html/src so the index.php is the entry
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/src|g' /etc/apache2/sites-available/000-default.conf
WORKDIR /var/www/html
COPY . .
RUN composer install
