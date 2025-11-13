# MBG Laravel Application - Deployment Summary

**Domain:** mbg.aventra.my.id  
**Target Environment:** VPS with Nginx Proxy Manager & Uptime Kuma

## 📁 Files Created for Deployment

### 1. Production Environment Configuration
- **File:** `.env.production`
- **Purpose:** Production-ready environment variables
- **Key Features:**
  - Configured for domain: mbg.aventra.my.id
  - MySQL database setup
  - Production security settings
  - Mail configuration template

### 2. Nginx Configuration
- **File:** `nginx.conf`
- **Purpose:** Backend server configuration for Nginx Proxy Manager
- **Key Features:**
  - Listens on port 8080 (for NPM proxying)
  - PHP-FPM integration
  - Security headers
  - Gzip compression
  - Static file caching

### 3. Deployment Automation Script
- **File:** `deploy.sh`
- **Purpose:** Automated deployment and updates
- **Commands Available:**
  - `./deploy.sh deploy` - Full deployment
  - `./deploy.sh update` - Code and dependencies update
  - `./deploy.sh migrate` - Database migrations only
  - `./deploy.sh cache` - Clear cache only
  - `./deploy.sh backup` - Create backup only

### 4. VPS Setup Script
- **File:** `setup-vps.sh`
- **Purpose:** Initial VPS server setup
- **Commands Available:**
  - `./setup-vps.sh all` - Complete setup
  - `./setup-vps.sh packages` - Install packages only
  - `./setup-vps.sh php` - Configure PHP only
  - `./setup-vps.sh database` - Setup database only
  - `./setup-vps.sh nginx` - Configure Nginx only

### 5. Comprehensive Deployment Guide
- **File:** `VPS_DEPLOYMENT.md`
- **Purpose:** Step-by-step deployment instructions
- **Sections:**
  - Server preparation
  - Database setup
  - Application deployment
  - Nginx Proxy Manager configuration
  - Uptime Kuma monitoring setup
  - Troubleshooting guide
  - Backup strategy

## 🚀 Quick Deployment Steps

### Step 1: Initial VPS Setup
```bash
# On your VPS
sudo ./setup-vps.sh all
```

### Step 2: Application Deployment
```bash
# Clone repository
cd /var/www/mbg
git clone https://github.com/ChaisarAbi/mbg.git .

# Setup environment
cp .env.production .env
nano .env  # Update database credentials

# Install and build
composer install --no-dev --optimize-autoloader
npm ci --only=production
npm run build

# Setup database
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force

# Set permissions
sudo chown -R www-data:www-data /var/www/mbg
sudo chmod -R 775 storage bootstrap/cache
```

### Step 3: Nginx Proxy Manager Configuration
1. Access NPM dashboard (usually http://your-server-ip:81)
2. Add Proxy Host:
   - Domain: `mbg.aventra.my.id`
   - Forward to: `localhost:8080`
   - Enable SSL with Let's Encrypt
   - Force SSL: Enabled

### Step 4: Uptime Kuma Monitoring
1. Add new monitor in Uptime Kuma
2. URL: `https://mbg.aventra.my.id`
3. Interval: 60 seconds

## 🔧 Architecture Overview

```
User Request → Nginx Proxy Manager → Nginx (port 8080) → PHP-FPM → Laravel App
```

### Port Configuration:
- **Nginx Proxy Manager:** Port 80/443 (SSL termination)
- **Backend Nginx:** Port 8080 (application server)
- **PHP-FPM:** Unix socket

## 🔒 Security Features

- SSL/TLS encryption via Let's Encrypt
- Security headers (X-Frame-Options, X-Content-Type-Options, etc.)
- File permission hardening
- Firewall configuration
- Database security
- Session encryption

## 📊 Monitoring & Maintenance

### Uptime Kuma Setup:
- Monitor application availability
- Configure notifications
- Track response times

### Regular Maintenance:
```bash
# Use deployment script for updates
sudo ./deploy.sh update

# Backup database
php artisan backup:run

# Monitor logs
tail -f /var/log/nginx/mbg_error.log
tail -f storage/logs/laravel.log
```

## 🆘 Troubleshooting

### Common Issues:
1. **Permission Errors:** Run `sudo chown -R www-data:www-data /var/www/mbg`
2. **Database Connection:** Check MySQL service and `.env` credentials
3. **Nginx Errors:** Run `sudo nginx -t` to test configuration
4. **PHP-FPM Issues:** Restart with `sudo systemctl restart php8.2-fpm`

### Log Locations:
- Nginx: `/var/log/nginx/mbg_*.log`
- Application: `/var/www/mbg/storage/logs/laravel.log`
- PHP-FPM: `/var/log/php8.2-fpm.log`

## ✅ Next Steps

1. **Update DNS:** Point `mbg.aventra.my.id` to your VPS IP
2. **Configure NPM:** Set up proxy host with SSL
3. **Test Deployment:** Visit https://mbg.aventra.my.id
4. **Setup Monitoring:** Configure Uptime Kuma
5. **Secure Database:** Change default passwords

## 📞 Support

For deployment issues, refer to:
- `VPS_DEPLOYMENT.md` - Detailed step-by-step guide
- GitHub repository: https://github.com/ChaisarAbi/mbg

Your MBG Laravel application is now ready for production deployment on your VPS with the domain mbg.aventra.my.id!
