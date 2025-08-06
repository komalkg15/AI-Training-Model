# Use official PHP image with Apache
FROM php:8.2-apache

# Copy all project files into the container
COPY . /var/www/html/

# Expose port 80 (Apache runs here)
EXPOSE 80
