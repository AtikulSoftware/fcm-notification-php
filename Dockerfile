FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y libpng-dev libonig-dev libxml2-dev
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite