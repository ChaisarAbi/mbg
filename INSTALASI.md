# Panduan Instalasi Cepat - Sistem Pengajuan Barang Dapur MBG

## 📋 Langkah Instalasi (5 Menit)

### 1. Pastikan Prerequisites Terpenuhi
- PHP 8.2+ sudah terinstall
- Composer sudah terinstall
- SQLite sudah tersedia (biasanya sudah include dengan PHP)

### 2. Setup Project
```bash
# Masuk ke direktori project
cd mbg

# Install dependencies Laravel
composer install

# Generate application key
php artisan key:generate

# Jalankan migrasi database
php artisan migrate

# Isi database dengan data contoh
php artisan db:seed
```

### 3. Jalankan Aplikasi
```bash
# Start development server
php artisan serve
```

### 4. Akses Aplikasi
- Buka browser: http://localhost:8000
- Login dengan akun default:

## 🔐 Akun Login Default

### Super Admin (Akses Penuh)
- **Email**: superadmin@mbg.com
- **Password**: password123

### Admin (Kelola Barang & Pengajuan)
- **Email**: admin@mbg.com  
- **Password**: password123

### Staf (Ajukan Barang)
- **Email**: staf@mbg.com
- **Password**: password123

## 🚀 Fitur yang Sudah Siap

### ✅ Backend Lengkap
- Database schema dengan 5 tabel
- Model dengan relationship
- Middleware autentikasi multi-role
- Seeder data contoh (10 barang, 5 user)

### ✅ Struktur Siap Development
- Migrations untuk semua tabel
- Model dengan method helper
- Middleware role protection
- Database relationships

## 🔧 Next Steps untuk Development

Untuk melengkapi aplikasi, perlu ditambahkan:

1. **Controllers** untuk masing-masing modul
2. **Views** (Blade templates) untuk interface
3. **Routes** untuk navigasi
4. **Form Validation** untuk input data
5. **Export Features** untuk laporan

## 📊 Data Contoh yang Tersedia

**10 Barang Dapur:**
- Beras Premium (Rp 15,000/kg)
- Minyak Goreng (Rp 25,000/liter)  
- Gula Pasir (Rp 12,000/kg)
- Garam Halus (Rp 8,000/kg)
- Telur Ayam (Rp 28,000/kg)
- Ayam Potong (Rp 35,000/kg)
- Ikan Segar (Rp 40,000/kg)
- Sayuran Segar (Rp 5,000/ikat)
- Bumbu Dapur (Rp 15,000/paket)
- Gas LPG 3kg (Rp 25,000/tabung)

## 🛠️ Testing Database

Untuk memastikan database berfungsi, jalankan:
```bash
# Test koneksi database
php artisan tinker

# Di dalam tinker, test query:
>>> \App\Models\User::count()
>>> \App\Models\Barang::count()
>>> \App\Models\Barang::first()
```

## ❓ Troubleshooting

### Error: SQLite database not found
```bash
# Buat file database SQLite
touch database/database.sqlite
```

### Error: Permission denied
```bash
# Set permission untuk storage dan cache
chmod -R 775 storage bootstrap/cache
```

### Error: Class not found
```bash
# Clear autoload dan cache
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

## 📞 Support

Sistem ini siap untuk dikembangkan lebih lanjut. Untuk pertanyaan teknis, periksa dokumentasi Laravel atau konsultasikan dengan developer.

---

**Sistem Informasi Pengajuan Barang Dapur MBG** - Siap Development!
