#!/bin/bash

# Dokploy Post-Deployment Script
# This script runs after the Docker containers are up and running

set -e

echo "🚀 Starting Dokploy Post-Deployment Script..."

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
while ! mysqladmin ping -h"mysql" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent; do
    echo "Waiting for MySQL..."
    sleep 2
done

echo "✅ MySQL is ready!"

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Generate application key if not exists
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Run database migrations
echo "🗃️ Running database migrations..."
php artisan migrate --force

# Seed database if needed (optional)
if [ "${SEED_DATABASE:-false}" = "true" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force
fi

# Clear and cache configuration
echo "⚡ Optimizing application..."
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build assets if using Vite
if [ -f "package.json" ]; then
    echo "🎨 Building frontend assets..."
    npm install --silent
    npm run build --silent
fi

# Set proper permissions
echo "🔒 Setting file permissions..."
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Create storage links
echo "🔗 Creating storage links..."
php artisan storage:link

echo "🎉 Dokploy Post-Deployment Script completed successfully!"
echo "📊 Application is ready at: ${APP_URL}"
