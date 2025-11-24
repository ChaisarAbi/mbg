#!/bin/bash

# Dokploy Single Container Post-Deployment Script
# This script runs inside the container after build

set -e

echo "🚀 Starting Dokploy Single Container Deployment..."

# Wait for services to be ready
echo "⏳ Waiting for PHP-FPM to be ready..."
while ! nc -z 127.0.0.1 9000; do
    sleep 1
done

echo "✅ PHP-FPM is ready!"

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

# Set proper permissions (in case they were reset)
echo "🔒 Setting file permissions..."
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Create storage links if not exists
echo "🔗 Creating storage links..."
php artisan storage:link

echo "🎉 Dokploy Single Container Deployment completed successfully!"
echo "📊 Application is ready at: ${APP_URL:-http://localhost}"
echo "🔍 Check logs: tail -f /var/log/supervisor/supervisord.log"
