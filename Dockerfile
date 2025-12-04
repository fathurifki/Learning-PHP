FROM php:8.2-apache

# Enable useful Apache/PHP extensions (minimal)
RUN a2enmod rewrite \
    && docker-php-ext-install pdo pdo_mysql

# Development PHP settings (OPcache timestamp checks, errors shown)
COPY ./php.ini /usr/local/etc/php/conf.d/dev.ini

WORKDIR /var/www/html

# Rely on bind mount from docker-compose for code (./src -> /var/www/html)
EXPOSE 80
