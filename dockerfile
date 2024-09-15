# Use the official PHP image from the Docker Hub
FROM php:8.0-apache

# Copy the current directory contents into the container at /var/www/html
COPY . /var/www/html

# Install additional PHP extensions if needed (optional)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Expose port 80 to allow web traffic
EXPOSE 80

# Start the Apache server
CMD ["apache2-foreground"]
