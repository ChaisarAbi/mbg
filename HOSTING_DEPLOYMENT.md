# Panduan Deployment ke Hosting

## 📋 Persiapan Sebelum Deployment

### **1. Pilih Jenis Hosting**
- **Shared Hosting**: Murah, mudah (cPanel)
- **VPS/Cloud**: Lebih fleksibel, perlu konfigurasi manual
- **Platform as a Service**: Heroku, Vercel, Railway

### **2. Persyaratan Sistem**
- **PHP**: 8.1 atau lebih tinggi
- **Database**: MySQL/PostgreSQL (disarankan) atau SQLite
- **Extensions PHP**: 
  - PDO
  - OpenSSL
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath
  - Fileinfo

## 🚀 Deployment ke Shared Hosting (cPanel)

### **Metode 1: Upload Manual via cPanel**

1. **Build Project untuk Production:**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm run build
   ```

2. **Upload File ke Hosting:**
   - Login ke cPanel → File Manager
   - Upload semua file ke `public_html` (atau subdomain folder)
   - **Kecuali**: `node_modules`, `.env`, `storage/logs`

3. **Setup Database:**
   - cPanel → MySQL Databases
   - Buat database baru
   - Buat user dan berikan akses ke database
   - Import struktur database (jika perlu)

4. **Konfigurasi Environment:**
   - Buat file `.env` di root folder
   - Copy isi dari `.env.example`
   - Sesuaikan konfigurasi:
     ```env
     APP_ENV=production
     APP_DEBUG=false
     APP_KEY=base64:... (generate dengan: php artisan key:generate)
     
     DB_CONNECTION=mysql
     DB_HOST=localhost
     DB_PORT=3306
     DB_DATABASE=nama_database
     DB_USERNAME=username_database
     DB_PASSWORD=password_database
     ```

5. **Set Permissions:**
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

6. **Run Migrations:**
   - Via SSH: `php artisan migrate --seed`
   - Atau via cPanel Cron Jobs

### **Metode 2: Git Deployment (jika hosting support)**

1. **Setup Git di Hosting:**
   ```bash
   git init
   git remote add production ssh://user@hostname.com:/path/to/project
   git push production main
   ```

2. **Post-Deployment Script:**
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan migrate --force
   npm run build
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## ☁️ Deployment ke Cloud/VPS

### **Setup Server (Ubuntu):**

1. **Update System:**
   ```bash
   sudo apt update && sudo apt upgrade -y
   ```

2. **Install PHP & Extensions:**
   ```bash
   sudo apt install php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip
   ```

3. **Install Database:**
   ```bash
   sudo apt install mysql-server
   ```

4. **Install Web Server (Nginx):**
   ```bash
   sudo apt install nginx
   ```

5. **Install Composer:**
   ```bash
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   ```

### **Deployment Process:**

1. **Clone Repository:**
   ```bash
   cd /var/www
   sudo git clone https://github.com/username/sistem-pengajuan-dapur-mbg.git
   sudo chown -R www-data:www-data sistem-pengajuan-dapur-mbg
   ```

2. **Install Dependencies:**
   ```bash
   cd sistem-pengajuan-dapur-mbg
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   ```

3. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup:**
   ```bash
   mysql -u root -p
   CREATE DATABASE mbg_database;
   CREATE USER 'mbg_user'@'localhost' IDENTIFIED BY 'password';
   GRANT ALL PRIVILEGES ON mbg_database.* TO 'mbg_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

5. **Run Migrations:**
   ```bash
   php artisan migrate --seed --force
   ```

6. **Set Permissions:**
   ```bash
   sudo chown -R abi:abi /var/www/sistem-pengajuan-dapur-mbg
   sudo chmod -R 755 storage
   sudo chmod -R 755 bootstrap/cache
   ```

### **Nginx Configuration:**

Buat file `/etc/nginx/sites-available/mbg`:
```nginx
server {
    listen 3500;
    server_name your-domain.com;
    root /var/www/sistem-pengajuan-dapur-mbg/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan site:
```bash
sudo ln -s /etc/nginx/sites-available/mbg /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🔧 Optimasi Production

### **Cache Configuration:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Environment Production:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mbg_database
DB_USERNAME=mbg_user
DB_PASSWORD=your_secure_password

# Session
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true

# Security
LOG_LEVEL=error
```

## 🛠️ Troubleshooting

### **Common Issues:**

1. **500 Internal Server Error:**
   - Check permissions: `chmod -R 755 storage bootstrap/cache`
   - Check `.env` configuration
   - Check PHP version compatibility

2. **Database Connection Error:**
   - Verify database credentials in `.env`
   - Check if database server is running
   - Ensure user has proper permissions

3. **Asset Loading Issues:**
   - Run `npm run build`
   - Check `public/build` folder exists
   - Verify Nginx/Apache configuration

4. **Migration Errors:**
   - Check database user permissions
   - Verify database exists
   - Run `php artisan migrate:fresh --seed` (development only)

### **Log Files:**
- Laravel logs: `storage/logs/laravel.log`
- Nginx logs: `/var/log/nginx/error.log`
- PHP-FPM logs: `/var/log/php8.1-fpm.log`

## 🔐 Security Checklist

- [ ] `APP_DEBUG=false`
- [ ] Strong `APP_KEY`
- [ ] Secure database credentials
- [ ] HTTPS enabled
- [ ] File permissions set correctly
- [ ] Regular backups configured
- [ ] Security headers in web server

## 📞 Support

Jika mengalami masalah:
1. Check log files untuk error details
2. Verify semua step di panduan ini
3. Pastikan requirements system terpenuhi
4. Test di local environment dulu

**Selamat! Sistem Anda siap digunakan di production 🚀**
