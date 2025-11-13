# VPS Deployment Guide for MBG Laravel Application

**Domain:** mbg.aventra.my.id  
**Technology Stack:** Laravel, MySQL, Nginx, PHP 8.2

## Prerequisites

### Server Requirements
- Ubuntu 20.04/22.04 LTS
- 2GB RAM minimum
- 20GB SSD storage
- PHP 8.2+
- MySQL 8.0+ or MariaDB 10.4+
- Nginx
- Git
- Node.js 18+ & npm

### Software Already Installed (Based on your setup)
- Nginx Proxy Manager
- Uptime Kuma

## Step 1: Server Preparation

### Install Required Packages
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath

# Install MySQL/MariaDB
sudo apt install -y mysql-server

# Install Node.js and npm
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install Git
sudo apt install -y git
```

### Configure PHP-FPM
```bash
# Edit PHP-FPM configuration
sudo nano /etc/php/8.2/fpm/php.ini

# Update these values:
memory_limit = 256M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

## Step 2: Database Setup

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Login to MySQL
sudo mysql -u root -p

# Create database and user
CREATE DATABASE mbg_production;
CREATE USER 'mbg_user'@'localhost' IDENTIFIED BY 'your_secure_password_here';
GRANT ALL PRIVILEGES ON mbg_production.* TO 'mbg_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Step 3: Application Setup

### Create Application Directory
```bash
# Create directory
sudo mkdir -p /var/www/mbg
sudo chown -R $USER:$USER /var/www/mbg

# Clone repository
cd /var/www/mbg
git clone https://github.com/ChaisarAbi/mbg.git .

# Copy production environment file
cp .env.production .env

# Edit environment variables
nano .env
```

### Update Environment Variables
Update these key values in `.env`:
```env
APP_URL=https://mbg.aventra.my.id
DB_DATABASE=mbg_production
DB_USERNAME=mbg_user
DB_PASSWORD=your_secure_password_here
```

### Install Dependencies and Setup
```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
npm ci --only=production

# Build assets
npm run build

# Generate application key
php artisan key:generate

# Run migrations and seeders
php artisan migrate --force
php artisan db:seed --force

# Set permissions
sudo chown -R www-data:www-data /var/www/mbg
sudo chmod -R 755 /var/www/mbg
sudo chmod -R 775 storage bootstrap/cache
```

## Step 4: Nginx Configuration

### Create Nginx Configuration
```bash
# Copy the provided nginx.conf to sites-available
sudo cp nginx.conf /etc/nginx/sites-available/mbg

# Create symbolic link
sudo ln -s /etc/nginx/sites-available/mbg /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

## Step 5: Nginx Proxy Manager Configuration

Since you're using Nginx Proxy Manager, configure it as follows:

1. **Access NPM Dashboard** (usually http://your-server-ip:81)
2. **Add Proxy Host:**
   - Domain Names: `mbg.aventra.my.id`
   - Forward Hostname/IP: `localhost`
   - Forward Port: `8080`
   - **SSL Tab:**
     - Force SSL: ✅
     - HTTP/2 Support: ✅
     - HSTS Enabled: ✅
     - HSTS Subdomains: ✅
   - **Advanced Tab:**
     ```nginx
     # Custom Nginx Configuration
     client_max_body_size 64M;
     
     # Security headers
     add_header X-Frame-Options "SAMEORIGIN" always;
     add_header X-Content-Type-Options "nosniff" always;
     add_header X-XSS-Protection "1; mode=block" always;
     add_header Referrer-Policy "strict-origin-when-cross-origin" always;
     ```

3. **SSL Certificate:**
   - Request SSL certificate
   - Use Let's Encrypt
   - Force SSL: Enabled

## Step 6: Uptime Kuma Monitoring

Add monitoring for your application in Uptime Kuma:

1. **Add New Monitor:**
   - Monitor Type: HTTP(s)
   - Friendly Name: MBG Application
   - URL: https://mbg.aventra.my.id
   - Interval: 60 seconds
   - Timeout: 30 seconds

2. **Notification Settings:**
   - Configure your preferred notification method
   - Set up alerts for downtime

## Step 7: Deployment Automation

### Make Deployment Script Executable
```bash
chmod +x deploy.sh
```

### Usage Examples:
```bash
# Full deployment
sudo ./deploy.sh deploy

# Update only
sudo ./deploy.sh update

# Run migrations only
sudo ./deploy.sh migrate

# Clear cache only
sudo ./deploy.sh cache
```

## Step 8: SSL Certificate (via NPM)

Since you're using Nginx Proxy Manager, SSL certificates are handled automatically:

1. In NPM dashboard, go to your proxy host
2. Click "SSL" tab
3. Select "Let's Encrypt"
4. Enter email for notifications
5. Enable "Force SSL" and "HTTP/2 Support"
6. Save configuration

## Step 9: Testing

### Test Application
```bash
# Test PHP configuration
php -v
php -m

# Test database connection
php artisan tinker
>>> \DB::connection()->getPdo()
```

### Test Web Access
Visit: https://mbg.aventra.my.id

## Step 10: Maintenance

### Regular Tasks
```bash
# Backup database
php artisan backup:run

# Clear cache (if needed)
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Check logs
tail -f /var/log/nginx/mbg_error.log
tail -f storage/logs/laravel.log
```

### Security Hardening
```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/mbg
sudo find /var/www/mbg -type f -exec chmod 644 {} \;
sudo find /var/www/mbg -type d -exec chmod 755 {} \;
sudo chmod -R 775 storage bootstrap/cache

# Configure firewall
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

## Troubleshooting

### Common Issues

1. **Permission Errors:**
   ```bash
   sudo chown -R www-data:www-data /var/www/mbg
   sudo chmod -R 775 storage bootstrap/cache
   ```

2. **Database Connection:**
   - Check MySQL service: `sudo systemctl status mysql`
   - Verify credentials in `.env` file

3. **Nginx Errors:**
   - Check syntax: `sudo nginx -t`
   - Check logs: `sudo tail -f /var/log/nginx/error.log`

4. **PHP-FPM Issues:**
   - Restart service: `sudo systemctl restart php8.2-fpm`
   - Check status: `sudo systemctl status php8.2-fpm`

### Log Locations
- Nginx Access: `/var/log/nginx/mbg_access.log`
- Nginx Error: `/var/log/nginx/mbg_error.log`
- Application: `/var/www/mbg/storage/logs/laravel.log`
- PHP-FPM: `/var/log/php8.2-fpm.log`

## Backup Strategy

### Database Backup
```bash
# Create backup script
sudo nano /usr/local/bin/backup-mbg.sh

# Add content:
#!/bin/bash
mysqldump -u mbg_user -p mbg_production > /var/backups/mbg/mbg_db_$(date +%Y%m%d_%H%M%S).sql

# Make executable
sudo chmod +x /usr/local/bin/backup-mbg.sh

# Add to crontab (daily at 2 AM)
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-mbg.sh
```

This deployment guide provides everything needed to deploy your MBG Laravel application to your VPS with the domain mbg.aventra.my.id, working seamlessly with your existing Nginx Proxy Manager and Uptime Kuma setup.
