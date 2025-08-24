# Use official PHP Apache image
FROM php:8.2-apache

# Enable PHP extensions if needed (mysqli, pdo, gd, etc.)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy project files into Apache web root
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
