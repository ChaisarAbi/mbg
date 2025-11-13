#!/bin/bash

# Deployment script for MBG Laravel Application
# Domain: mbg.aventra.my.id

set -e

echo "🚀 Starting deployment of MBG Laravel Application..."

# Variables
APP_DIR="/var/www/mbg"
BACKUP_DIR="/var/backups/mbg"
DATE=$(date +%Y%m%d_%H%M%S)

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to log messages
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    error "Please run as root or with sudo"
    exit 1
fi

# Create backup
backup() {
    log "Creating backup..."
    mkdir -p $BACKUP_DIR
    tar -czf $BACKUP_DIR/mbg_backup_$DATE.tar.gz -C $APP_DIR .
    log "Backup created: $BACKUP_DIR/mbg_backup_$DATE.tar.gz"
}

# Update code
update_code() {
    log "Updating code from Git..."
    cd $APP_DIR
    
    # Pull latest changes
    git fetch origin
    git reset --hard origin/main
    
    log "Code updated successfully"
}

# Install dependencies
install_dependencies() {
    log "Installing PHP dependencies..."
    cd $APP_DIR
    composer install --no-dev --optimize-autoloader
    
    log "Installing Node.js dependencies..."
    npm ci --only=production
    
    log "Building assets..."
    npm run build
    
    log "Dependencies installed successfully"
}

# Run migrations
run_migrations() {
    log "Running database migrations..."
    cd $APP_DIR
    php artisan migrate --force
    
    log "Migrations completed successfully"
}

# Clear cache
clear_cache() {
    log "Clearing application cache..."
    cd $APP_DIR
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    
    log "Cache cleared successfully"
}

# Set permissions
set_permissions() {
    log "Setting file permissions..."
    cd $APP_DIR
    
    # Set ownership to www-data
    chown -R www-data:www-data .
    
    # Set directory permissions
    find . -type d -exec chmod 755 {} \;
    
    # Set file permissions
    find . -type f -exec chmod 644 {} \;
    
    # Set storage and bootstrap/cache permissions
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    
    log "Permissions set successfully"
}

# Restart services
restart_services() {
    log "Restarting PHP-FPM..."
    systemctl restart php8.2-fpm
    
    log "PHP-FPM restarted successfully"
}

# Main deployment function
deploy() {
    log "Starting deployment process..."
    
    # Create backup
    backup
    
    # Put application in maintenance mode
    cd $APP_DIR
    php artisan down
    
    # Update code
    update_code
    
    # Install dependencies
    install_dependencies
    
    # Run migrations
    run_migrations
    
    # Clear cache
    clear_cache
    
    # Set permissions
    set_permissions
    
    # Take application out of maintenance mode
    php artisan up
    
    # Restart services
    restart_services
    
    log "✅ Deployment completed successfully!"
    log "📱 Application is now live at: https://mbg.aventra.my.id"
}

# Handle command line arguments
case "$1" in
    deploy)
        deploy
        ;;
    backup)
        backup
        ;;
    update)
        update_code
        install_dependencies
        clear_cache
        restart_services
        ;;
    migrate)
        run_migrations
        clear_cache
        ;;
    cache)
        clear_cache
        ;;
    *)
        echo "Usage: $0 {deploy|backup|update|migrate|cache}"
        echo ""
        echo "Commands:"
        echo "  deploy   - Full deployment (backup, update, install, migrate, cache)"
        echo "  backup   - Create backup only"
        echo "  update   - Update code and dependencies only"
        echo "  migrate  - Run migrations only"
        echo "  cache    - Clear cache only"
        exit 1
        ;;
esac
