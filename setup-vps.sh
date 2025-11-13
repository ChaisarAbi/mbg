#!/bin/bash

# Quick VPS Setup Script for MBG Laravel Application
# This script helps with initial server setup

set -e

echo "🚀 Starting VPS setup for MBG Laravel Application..."

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

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

install_packages() {
    log "Installing required packages..."
    
    # Update system
    apt update && apt upgrade -y
    
    # Install PHP and extensions
    apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
        php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath
    
    # Install MySQL
    apt install -y mysql-server
    
    # Install Node.js
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
    apt install -y nodejs
    
    # Install Composer
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
    
    # Install Git
    apt install -y git
    
    # Install Nginx (if not already installed)
    apt install -y nginx
    
    log "Packages installed successfully"
}

configure_php() {
    log "Configuring PHP..."
    
    # Update PHP configuration
    sed -i 's/memory_limit = .*/memory_limit = 256M/' /etc/php/8.2/fpm/php.ini
    sed -i 's/upload_max_filesize = .*/upload_max_filesize = 64M/' /etc/php/8.2/fpm/php.ini
    sed -i 's/post_max_size = .*/post_max_size = 64M/' /etc/php/8.2/fpm/php.ini
    sed -i 's/max_execution_time = .*/max_execution_time = 300/' /etc/php/8.2/fpm/php.ini
    
    # Restart PHP-FPM
    systemctl restart php8.2-fpm
    
    log "PHP configured successfully"
}

setup_database() {
    log "Setting up database..."
    
    # Secure MySQL (minimal interactive setup)
    mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '';"
    mysql -e "DELETE FROM mysql.user WHERE User='';"
    mysql -e "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');"
    mysql -e "DROP DATABASE IF EXISTS test;"
    mysql -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';"
    mysql -e "FLUSH PRIVILEGES;"
    
    # Create database and user
    mysql -e "CREATE DATABASE IF NOT EXISTS mbg_production;"
    mysql -e "CREATE USER IF NOT EXISTS 'mbg_user'@'localhost' IDENTIFIED BY 'mbg_password_123';"
    mysql -e "GRANT ALL PRIVILEGES ON mbg_production.* TO 'mbg_user'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"
    
    log "Database setup completed"
    warn "⚠️  Please change the database password in production!"
}

setup_application() {
    log "Setting up application..."
    
    # Create application directory
    mkdir -p /var/www/mbg
    chown -R $SUDO_USER:$SUDO_USER /var/www/mbg
    
    log "Application directory created at /var/www/mbg"
    log "Please manually:"
    log "1. Clone your repository: cd /var/www/mbg && git clone https://github.com/ChaisarAbi/mbg.git ."
    log "2. Copy .env.production to .env and update database credentials"
    log "3. Run: composer install --no-dev --optimize-autoloader"
    log "4. Run: npm ci --only=production && npm run build"
    log "5. Run: php artisan key:generate && php artisan migrate --force"
}

configure_nginx() {
    log "Configuring Nginx..."
    
    # Copy Nginx configuration
    if [ -f "nginx.conf" ]; then
        cp nginx.conf /etc/nginx/sites-available/mbg
        ln -sf /etc/nginx/sites-available/mbg /etc/nginx/sites-enabled/
        
        # Remove default site
        rm -f /etc/nginx/sites-enabled/default
        
        # Test and restart Nginx
        nginx -t
        systemctl restart nginx
        
        log "Nginx configured successfully"
    else
        error "nginx.conf file not found!"
    fi
}

setup_firewall() {
    log "Setting up firewall..."
    
    # Install UFW if not present
    apt install -y ufw
    
    # Configure firewall
    ufw allow ssh
    ufw allow 'Nginx Full'
    ufw --force enable
    
    log "Firewall configured"
}

show_next_steps() {
    echo ""
    log "✅ VPS setup completed!"
    echo ""
    log "📋 Next Steps:"
    log "1. Deploy your application to /var/www/mbg"
    log "2. Configure Nginx Proxy Manager:"
    log "   - Domain: mbg.aventra.my.id"
    log "   - Forward to: localhost:8080"
    log "   - Enable SSL"
    log "3. Set up Uptime Kuma monitoring"
    log "4. Update database password in .env file"
    echo ""
    log "📚 For detailed instructions, see: VPS_DEPLOYMENT.md"
}

# Main execution
case "${1:-all}" in
    packages)
        install_packages
        ;;
    php)
        configure_php
        ;;
    database)
        setup_database
        ;;
    nginx)
        configure_nginx
        ;;
    firewall)
        setup_firewall
        ;;
    all)
        install_packages
        configure_php
        setup_database
        configure_nginx
        setup_firewall
        show_next_steps
        ;;
    *)
        echo "Usage: $0 {packages|php|database|nginx|firewall|all}"
        echo ""
        echo "Commands:"
        echo "  packages - Install required packages only"
        echo "  php      - Configure PHP only"
        echo "  database - Setup database only"
        echo "  nginx    - Configure Nginx only"
        echo "  firewall - Setup firewall only"
        echo "  all      - Complete setup (default)"
        exit 1
        ;;
esac
