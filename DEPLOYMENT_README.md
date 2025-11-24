# Deployment Guide - Sistem Pengajuan Barang Dapur MBG

## 📋 Overview

This project is a Laravel-based web application for managing item requests and invoices for MBG Kitchen. It's ready for deployment to various platforms including Dokploy, VPS, and traditional hosting.

## 🚀 Quick Deployment Options

### 1. Dokploy (Recommended)
**File**: `DOKPLOY_DEPLOYMENT.md`
- Complete step-by-step guide
- Docker-based deployment
- Automatic scaling and monitoring

### 2. Traditional VPS
**File**: `VPS_DEPLOYMENT.md`
- Manual server setup
- Nginx + PHP-FPM configuration
- Database setup instructions

### 3. Docker Deployment
**File**: `DOCKER_DEPLOYMENT.md`
- Containerized deployment
- Multi-service architecture
- Development and production setups

## 📁 Deployment Files Structure

```
├── DOKPLOY_DEPLOYMENT.md          # Complete Dokploy deployment guide
├── docker-compose.dokploy.yml     # Optimized Docker compose for Dokploy
├── .env.dokploy                   # Environment template for Dokploy
├── dokploy-post-deploy.sh         # Post-deployment automation script
├── VPS_DEPLOYMENT.md              # Traditional VPS deployment guide
├── DOCKER_DEPLOYMENT.md           # Docker deployment guide
├── docker-compose.yml             # Development Docker compose
├── Dockerfile                     # Application Docker image
├── nginx.conf                     # Nginx configuration
└── deploy.sh                      # Manual deployment script
```

## 🔧 Prerequisites

### For All Deployments
- PHP 8.2+
- Composer
- MySQL 8.0+ or SQLite
- Node.js 18+ (for asset building)

### For Docker Deployments
- Docker Engine 20.10+
- Docker Compose 2.0+

### For Dokploy
- Dokploy account
- GitHub repository connected
- Domain (optional)

## 🎯 Recommended Deployment Strategy

### Development
```bash
# Local development with Docker
docker-compose up -d

# Or traditional setup
composer install
php artisan serve
```

### Staging/Production
1. **Dokploy** (Easiest & Most Reliable)
   - Automated deployment
   - Built-in monitoring
   - Easy scaling

2. **VPS with Docker** (Flexible)
   - Full control over infrastructure
   - Custom configurations
   - Manual scaling

3. **Traditional VPS** (Legacy)
   - Direct server access
   - Manual optimization
   - More maintenance required

## 📊 Application Features

- ✅ Multi-role authentication (Staf, Admin, Super Admin)
- ✅ Item request management
- ✅ Invoice generation
- ✅ Price management
- ✅ Reporting system
- ✅ PDF export capabilities

## 🔐 Security Considerations

### Environment Variables
- Never commit `.env` files
- Use strong database passwords
- Set `APP_DEBUG=false` in production
- Use HTTPS/SSL in production

### Database Security
- Use strong MySQL passwords
- Regular backups
- Database user with minimal privileges

### Application Security
- Keep Laravel and dependencies updated
- Regular security audits
- Input validation and sanitization

## 📈 Performance Optimization

### Caching
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database
- Proper indexing
- Query optimization
- Regular maintenance

### Assets
- Production asset building
- CDN integration (optional)
- Image optimization

## 🛠️ Maintenance

### Regular Tasks
- Update dependencies
- Database backups
- Log monitoring
- Security patches

### Monitoring
- Application logs
- Database performance
- Server resources
- Error tracking

## 🆘 Troubleshooting

### Common Issues
1. **Database Connection**
   - Check environment variables
   - Verify database service is running
   - Test network connectivity

2. **Asset Loading**
   - Run `npm run build`
   - Check public/build directory
   - Verify Vite configuration

3. **Permissions**
   - Set proper storage permissions
   - Check file ownership
   - Verify directory permissions

### Logs Location
- Application: `storage/logs/laravel.log`
- Nginx: `/var/log/nginx/`
- Docker: `docker logs <container_name>`

## 🔄 Update Process

### For Dokploy
1. Push changes to GitHub
2. Dokploy automatically rebuilds and redeploys
3. Monitor deployment logs

### For Manual Deployments
1. Pull latest changes
2. Run `composer install`
3. Run database migrations
4. Build assets
5. Clear caches

## 📞 Support

- **Documentation**: Check respective deployment guides
- **Issues**: GitHub repository issues
- **Community**: Laravel community forums
- **Professional**: Contact development team

---

## 🎉 Deployment Checklist

### Pre-Deployment
- [ ] Environment variables configured
- [ ] Database setup completed
- [ ] SSL certificates ready (for production)
- [ ] Domain configured (if using custom domain)

### Post-Deployment
- [ ] Application accessible
- [ ] Database migrations applied
- [ ] Assets built and loading
- [ ] User authentication working
- [ ] All features tested
- [ ] Monitoring configured
- [ ] Backup strategy implemented

### Maintenance
- [ ] Regular updates scheduled
- [ ] Backup verification
- [ ] Performance monitoring
- [ ] Security updates applied

---

**Last Updated**: November 2025  
**Version**: 1.0.0  
**Compatibility**: Laravel 12, PHP 8.2+, MySQL 8.0+
