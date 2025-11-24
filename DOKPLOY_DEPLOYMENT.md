# Panduan Deployment ke Dokploy - Sistem Pengajuan Barang Dapur MBG

## 📋 Prasyarat

Sebelum deployment, pastikan:
- [ ] Akun Dokploy sudah aktif
- [ ] Domain sudah disiapkan (jika menggunakan custom domain)
- [ ] Database MySQL tersedia (bisa menggunakan database Dokploy atau external)
- [ ] Repository GitHub project sudah terhubung ke Dokploy

## 🚀 Langkah-langkah Deployment

### 1. Persiapan Repository

Pastikan repository GitHub sudah memiliki file-file berikut:
- ✅ `Dockerfile`
- ✅ `docker-compose.yml` 
- ✅ `.env.dokploy` (akan dibuat)
- ✅ `DOKPLOY_DEPLOYMENT.md` (file ini)

### 2. Konfigurasi di Dokploy

#### A. Buat Project Baru
1. Login ke dashboard Dokploy
2. Klik "New Project"
3. Pilih "From Git Repository"
4. Connect repository GitHub Anda
5. Pilih branch yang akan di-deploy (biasanya `main` atau `master`)

#### B. Environment Variables
Set environment variables berikut di Dokploy:

```bash
# Application
APP_NAME="Sistem Pengajuan Barang Dapur MBG"
APP_ENV=production
APP_KEY=base64:XzpqkWFIWEdxC1RE5jlfxGupGO4tkz8maAz5hcSVpXU=
APP_DEBUG=false
APP_URL=https://your-domain.dokploy.com

# Database (sesuaikan dengan konfigurasi Dokploy)
DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=mbg_database
DB_USERNAME=mbg_user
DB_PASSWORD=your-secure-password

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database

# Mail (opsional)
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@mbg.com
MAIL_FROM_NAME="Sistem MBG"

# Vite
VITE_APP_NAME="${APP_NAME}"
```

#### C. Build Settings
- **Build Method**: Docker Compose
- **Docker Compose File**: `docker-compose.yml`
- **Build Context**: `.` (root directory)

### 3. Konfigurasi Database

#### Opsi A: Database Dokploy
1. Di Dokploy, buka tab "Databases"
2. Klik "Create Database"
3. Isi detail database:
   - Name: `mbg_database`
   - Username: `mbg_user`
   - Password: `secure-password-here`
4. Catat host, port, dan credentials untuk environment variables

#### Opsi B: External Database
Gunakan MySQL/MariaDB dari provider lain dan sesuaikan environment variables.

### 4. Deployment Process

#### A. Build & Deploy
1. Klik "Deploy" di project Dokploy
2. Dokploy akan:
   - Pull code dari GitHub
   - Build Docker images
   - Run docker-compose up
   - Execute post-deployment scripts

#### B. Post-Deployment Commands
Setelah deployment selesai, jalankan command berikut di terminal Dokploy:

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader

# Generate application key (jika belum ada)
php artisan key:generate

# Run database migrations
php artisan migrate --force

# Seed database (opsional - untuk data contoh)
php artisan db:seed --force

# Optimize application
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build assets (jika menggunakan Vite)
npm install && npm run build
```

### 5. Domain & SSL Configuration

#### A. Custom Domain (Opsional)
1. Di Dokploy, buka tab "Domains"
2. Tambahkan domain Anda
3. Update DNS records untuk mengarah ke server Dokploy
4. Enable SSL untuk domain tersebut

#### B. Default Domain
Dokploy akan memberikan domain default seperti: `your-project.dokploy.com`

### 6. Monitoring & Maintenance

#### A. Logs Monitoring
- Akses logs melalui tab "Logs" di Dokploy
- Monitor application logs dan error logs

#### B. Backup Strategy
1. **Database Backup**:
   - Setup automatic backup di Dokploy
   - Atau gunakan external backup service

2. **Application Backup**:
   - GitHub repository sebagai backup code
   - Environment variables disimpan di Dokploy

#### C. Updates & Maintenance
- Untuk update aplikasi, push perubahan ke GitHub
- Dokploy akan otomatis rebuild dan redeploy
- Untuk update major, lakukan testing di staging environment terlebih dahulu

## 🔧 Troubleshooting

### Common Issues & Solutions

#### 1. Build Failed
**Problem**: Docker build gagal
**Solution**:
- Periksa Dockerfile syntax
- Pastikan semua dependencies tersedia
- Check build logs di Dokploy

#### 2. Database Connection Error
**Problem**: Aplikasi tidak bisa connect ke database
**Solution**:
- Periksa environment variables DB_*
- Pastikan database service running
- Check network connectivity

#### 3. 500 Internal Server Error
**Problem**: Aplikasi error setelah deployment
**Solution**:
- Check application logs
- Pastikan `APP_DEBUG=false` di production
- Verifikasi file permissions
- Jalankan `php artisan optimize`

#### 4. Asset Loading Issues
**Problem**: CSS/JS tidak loading
**Solution**:
- Jalankan `npm run build`
- Pastikan VITE_* environment variables benar
- Check public/build directory

## 📊 Performance Optimization

### 1. Caching
```bash
# Setelah deployment
php artisan config:cache
php artisan route:cache  
php artisan view:cache
```

### 2. OpCache (PHP)
Pastikan OpCache enabled di php.ini:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0 ; Set ke 1 di development
```

### 3. Database Optimization
- Setup database indexes
- Regular maintenance (optimize tables)
- Query optimization

## 🔐 Security Best Practices

### 1. Environment Security
- Jangan expose `.env` file
- Gunakan strong passwords untuk database
- Regular security updates

### 2. Application Security
- Keep Laravel dan dependencies updated
- Use HTTPS/SSL
- Implement proper input validation
- Regular security audits

### 3. Access Control
- Limit access ke Dokploy dashboard
- Use strong authentication
- Regular access reviews

## 📞 Support

Jika mengalami masalah:
1. Check Dokploy documentation
2. Review application logs
3. Contact Dokploy support
4. Check GitHub issues

---

**Deployment Checklist**:
- [ ] Repository connected to Dokploy
- [ ] Environment variables configured
- [ ] Database setup completed
- [ ] Build successful
- [ ] Post-deployment commands executed
- [ ] Application accessible
- [ ] SSL configured (if using custom domain)
- [ ] Monitoring setup
- [ ] Backup strategy implemented

**Last Updated**: November 2025
