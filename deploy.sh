#!/bin/bash

# Production Deployment Script for CodeIgniter 4
# Make sure to customize this script according to your server setup

echo "Starting production deployment..."

# Update from repository
echo "Pulling latest changes..."
git pull origin main

# Install/Update dependencies
echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Clear and warm up caches
echo "Clearing caches..."
php spark cache:clear
php spark config:cache

# Run database migrations
echo "Running database migrations..."
php spark migrate

# Set proper permissions
echo "Setting file permissions..."
chmod -R 755 writable/
chmod -R 644 writable/cache/
chmod -R 644 writable/logs/
chmod -R 644 writable/session/
chmod -R 644 writable/uploads/

# Restart web server (adjust according to your setup)
echo "Restarting web server..."
# sudo systemctl restart apache2
# sudo systemctl restart nginx
# sudo service php8.1-fpm restart

echo "Deployment completed successfully!"
echo "Don't forget to:"
echo "1. Update your .env file with production values"
echo "2. Generate a secure encryption key"
echo "3. Configure your web server properly"
echo "4. Set up SSL certificate"
echo "5. Configure database backups"