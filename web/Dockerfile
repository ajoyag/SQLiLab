FROM php:8.1-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli pdo_mysql

# Copy app
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
