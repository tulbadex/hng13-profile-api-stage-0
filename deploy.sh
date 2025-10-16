#!/bin/bash

# Laravel deployment script for EC2
cd /var/www/profile-api

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Clear all caches first
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache config and routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Reload web server
sudo systemctl reload nginx