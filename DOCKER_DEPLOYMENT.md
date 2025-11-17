# Docker Deployment Guide for MBG Laravel Application

## 🐳 Overview

This guide provides comprehensive instructions for deploying the MBG Laravel application using Docker containers. The setup includes a multi-container architecture with MySQL, Nginx, PHP-FPM, Redis, and optional services.

## 📋 Prerequisites

### System Requirements
- Docker Engine 20.10+
- Docker Compose 2.0+
- 2GB RAM minimum
- 20GB free disk space

### Required Files
- `Dockerfile` - PHP-FPM application container
- `docker-compose.yml` - Multi-service orchestration
- `.env.docker` - Environment template
- `docker-deploy.sh` - Deployment automation script

## 🚀 Quick Start

### 1. Clone and Prepare
```bash
# Clone repository
git clone https://github.com/ChaisarAbi/mbg.git
cd mbg

# Make deployment script executable
chmod +x docker-deploy.sh
```

### 2. Configure Environment
```bash
# Copy environment template
cp .env.docker .env

# Edit environment variables
nano .env
```

Update these key values in `.env`:
```env
APP_URL=https://mbg.aventra.my.id
DB_DATABASE=mbg_production
DB_USERNAME=mbg_user
DB_PASSWORD=your_secure_password_here
DB_ROOT_PASSWORD=your_secure_root_password_here
```

### 3. Deploy Application
```bash
# Start all services
./docker-deploy.sh up
```

### 4. Access Application
- **Main Application**: http://localhost:8080
- **PHPMyAdmin**: http://localhost:8081
- **MySQL**: localhost:3306
- **Redis**: localhost:6379

## 🏗️ Architecture

### Container Services

| Service | Image | Port | Purpose |
|---------|-------|------|---------|
| `app` | Custom PHP-FPM | 9000 | Laravel application |
| `nginx` | nginx:alpine | 8080 | Web server |
| `mysql` | mysql:8.0 | 3306 | Database |
| `redis` | redis:alpine | 6379 | Cache & sessions |
| `phpmyadmin` | phpmyadmin | 8081 | Database management |
| `node` | node:18-alpine | - | Asset building |

### Network Configuration
- **Network**: `mbg_network` (bridge)
- **Volume**: `mysql_data` (persistent database storage)

## 📁 File Structure

```
mbg/
├── Dockerfile                 # PHP-FPM application container
├── docker-compose.yml         # Multi-service orchestration
├── .env.docker               # Environment template
├── docker-deploy.sh          # Deployment automation
├── docker/
│   ├── nginx/
│   │   └── nginx.conf        # Nginx configuration
│   └── php/
│       └── php.ini           # PHP configuration
├── backups/                  # Database backups
└── README.md
```

## 🔧 Configuration Details

### Dockerfile
- PHP 8.2 with FPM
- Required PHP extensions installed
- Composer for dependency management
- Node.js for asset building
- Proper file permissions

### Nginx Configuration
- Security headers
- Gzip compression
- Static file caching
- PHP-FPM integration
- File upload limits (64MB)

### PHP Configuration
- Memory limit: 256MB
- Upload size: 64MB
- Execution time: 300s
- OpCache enabled
- Security settings hardened

## 🛠️ Deployment Commands

### Using the Deployment Script
```bash
# Full deployment
./docker-deploy.sh up

# Stop services
./docker-deploy.sh down

# Build images
./docker-deploy.sh build

# View logs
./docker-deploy.sh logs
./docker-deploy.sh logs app    # Specific service

# Execute commands
./docker-deploy.sh exec-app php artisan tinker
./docker-deploy.sh exec-db mysql -u root -p

# Database operations
./docker-deploy.sh backup
./docker-deploy.sh restore backup_file.sql

# Application setup
./docker-deploy.sh setup

# Check status
./docker-deploy.sh status
```

### Manual Docker Commands
```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# Build images
docker-compose build

# View logs
docker-compose logs -f

# Execute commands
docker-compose exec app php artisan migrate
docker-compose exec mysql mysql -u root -p
```

## 🔒 Security Considerations

### Environment Variables
- Never commit `.env` file to version control
- Use strong passwords for database
- Enable HTTPS in production
- Set proper file permissions

### Network Security
- Containers communicate via internal network
- Only necessary ports exposed
- Nginx handles SSL termination
- Security headers enabled

### Database Security
- Root password protected
- Separate application user
- Data persisted in volumes
- Regular backups

## 📊 Monitoring & Logs

### Access Logs
```bash
# Application logs
./docker-deploy.sh logs app

# Nginx logs
./docker-deploy.sh logs nginx

# Database logs
./docker-deploy.sh logs mysql

# All services
./docker-deploy.sh logs
```

### Log Locations
- **Application**: `storage/logs/laravel.log`
- **Nginx**: Container logs
- **PHP-FPM**: Container logs
- **MySQL**: Container logs

## 🔄 Maintenance

### Regular Tasks
```bash
# Backup database (daily)
./docker-deploy.sh backup

# Update application
git pull
./docker-deploy.sh up

# Clear cache
./docker-deploy.sh exec-app php artisan cache:clear

# Check disk usage
docker system df
```

### Database Backups
```bash
# Create backup
./docker-deploy.sh backup

# List backups
ls -la backups/

# Restore backup
./docker-deploy.sh restore mbg_backup_20250101_120000.sql
```

## 🚨 Troubleshooting

### Common Issues

**1. Port Conflicts**
```bash
# Check used ports
netstat -tulpn | grep :8080

# Change ports in docker-compose.yml
ports:
  - "8081:80"  # Change external port
```

**2. Permission Errors**
```bash
# Fix file permissions
./docker-deploy.sh exec-app chown -R www-data:www-data /var/www
./docker-deploy.sh exec-app chmod -R 775 storage bootstrap/cache
```

**3. Database Connection**
```bash
# Check database status
./docker-deploy.sh exec-db mysqladmin ping

# Test connection from app
./docker-deploy.sh exec-app php artisan tinker
>>> \DB::connection()->getPdo()
```

**4. Container Issues**
```bash
# Restart specific service
docker-compose restart app

# Rebuild and restart
docker-compose down
docker-compose up -d --build
```

### Debug Mode
```bash
# Enable debug mode in .env
APP_DEBUG=true

# View detailed logs
./docker-deploy.sh logs app
```

## 🌐 Production Deployment

### VPS Deployment Steps

1. **Server Preparation**
   ```bash
   # Install Docker
   curl -fsSL https://get.docker.com -o get-docker.sh
   sh get-docker.sh

   # Install Docker Compose
   sudo curl -L "https://github.com/docker/compose/releases/download/v2.24.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
   sudo chmod +x /usr/local/bin/docker-compose
   ```

2. **Application Setup**
   ```bash
   # Clone repository
   git clone https://github.com/ChaisarAbi/mbg.git
   cd mbg

   # Configure environment
   cp .env.docker .env
   nano .env  # Update production values

   # Deploy
   chmod +x docker-deploy.sh
   ./docker-deploy.sh up
   ```

3. **Nginx Proxy Manager Integration**
   - Add proxy host for your domain
   - Point to `localhost:8080`
   - Enable SSL certificates
   - Configure security headers

4. **Monitoring**
   - Set up Uptime Kuma
   - Configure alerts
   - Monitor resource usage

### Performance Optimization

**1. Resource Limits**
```yaml
# In docker-compose.yml
services:
  app:
    deploy:
      resources:
        limits:
          memory: 512M
        reservations:
          memory: 256M
```

**2. Caching**
- Redis for sessions and cache
- Nginx static file caching
- Browser caching headers

**3. Database Optimization**
- Regular maintenance
- Proper indexing
- Query optimization

## 📞 Support

### Getting Help
- Check application logs: `./docker-deploy.sh logs`
- Verify container status: `./docker-deploy.sh status`
- Test database connection: `./docker-deploy.sh exec-app php artisan tinker`

### Common Commands Reference
```bash
# Quick status check
./docker-deploy.sh status

# Application maintenance
./docker-deploy.sh exec-app php artisan cache:clear
./docker-deploy.sh exec-app php artisan config:cache

# Database operations
./docker-deploy.sh exec-db mysql -u root -p
./docker-deploy.sh backup

# Service management
./docker-deploy.sh up    # Start
./docker-deploy.sh down  # Stop
./docker-deploy.sh logs  # Monitor
```

## 🎯 Next Steps

1. **Configure Domain**: Update `APP_URL` in `.env`
2. **Enable SSL**: Use Nginx Proxy Manager
3. **Set Up Monitoring**: Configure Uptime Kuma
4. **Implement Backups**: Schedule regular database backups
5. **Scale Services**: Adjust resource limits as needed

---

**Status**: ✅ **DOCKER DEPLOYMENT READY**  
**Environment**: Production-ready  
**Security**: Hardened configuration  
**Monitoring**: Comprehensive logging

*Deployment optimized for MBG Laravel Application - Scalable and maintainable Docker setup*
