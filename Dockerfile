# Use official PHP with Apache image
FROM php:8.2-apache

# Enable mysqli extension (for MySQL)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy project files into container
COPY . /var/www/html/

# Set permissions (optional but recommended)
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
