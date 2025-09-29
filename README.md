# Sistem Informasi Pengajuan Barang & Invoice Dapur MBG

Sistem informasi berbasis web untuk pengelolaan pengajuan barang dan invoice pada Dapur MBG dengan multi-user dan penyesuaian harga pasar.

## 🚀 Fitur Utama

### 👥 Peran & Hak Akses
- **👨‍🍳 Staf Dapur**: Login, mengajukan barang, melihat status pengajuan & invoice
- **🛠️ Admin**: Login, kelola data barang, verifikasi pengajuan, update harga barang, buat & cetak invoice, lihat laporan
- **🧑‍💼 Super Admin**: Semua hak admin + kelola akun user, lihat log aktivitas, atur profil sistem, lihat laporan menyeluruh

### 📦 Modul Aplikasi
- 👤 Autentikasi multi-role
- 📦 CRUD data barang dengan update harga
- 📤 Sistem pengajuan barang oleh staf
- ✅ Verifikasi pengajuan oleh admin
- 🧾 Invoice otomatis dari pengajuan disetujui
- 📊 Laporan pengajuan, invoice, dan perubahan harga
- ⚙️ Pengelolaan sistem untuk Super Admin

## 🛠️ Teknologi yang Digunakan

- **Backend**: Laravel 12
- **Database**: SQLite
- **Frontend**: Blade Templates
- **Authentication**: Laravel Auth dengan multi-role

## 📋 Struktur Database

### Tabel-tabel yang telah dibuat:
1. **users** - Data pengguna dengan role (staf, admin, super_admin)
2. **barangs** - Data barang dengan harga saat ini
3. **pengajuans** - Data pengajuan barang
4. **pengajuan_items** - Detail item dalam pengajuan
5. **invoices** - Data invoice yang dihasilkan dari pengajuan disetujui

## 🔧 Instalasi & Setup

### Prerequisites
- PHP 8.2+
- Composer
- SQLite

### Langkah Instalasi

1. **Clone atau extract project**
```bash
cd mbg
```

2. **Install dependencies**
```bash
composer install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Jalankan migrasi dan seeder**
```bash
php artisan migrate
php artisan db:seed
```

5. **Jalankan server development**
```bash
php artisan serve
```

## 👤 Akun Default

Setelah menjalankan seeder, akun berikut tersedia:

### Super Admin
- Email: `superadmin@mbg.com`
- Password: `password123`
- Role: `super_admin`

### Admin
- Email: `admin@mbg.com`
- Password: `password123`
- Role: `admin`

### Staf
- Email: `staf@mbg.com`
- Password: `password123`
- Role: `staf`

- Email: `ahmad@mbg.com`
- Password: `password123`
- Role: `staf`

- Email: `siti@mbg.com`
- Password: `password123`
- Role: `staf`

## 📊 Data Barang Contoh

Sistem sudah dilengkapi dengan 10 data barang contoh:
- Beras Premium, Minyak Goreng, Gula Pasir, Garam Halus
- Telur Ayam, Ayam Potong, Ikan Segar, Sayuran Segar
- Bumbu Dapur, Gas LPG 3kg

## 🔐 Middleware Role

Sistem menggunakan middleware `CheckRole` untuk proteksi akses:
```php
// Contoh penggunaan di routes
Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {
    // Routes untuk admin dan super admin
});
```

## 📈 Fitur Lanjutan yang Tersedia

### Untuk Staf:
- Form pengajuan barang dengan pilihan barang dan jumlah
- Melihat status pengajuan (pending, approved, rejected)
- Melihat invoice yang telah dibuat

### Untuk Admin:
- Dashboard dengan statistik pengajuan
- Kelola data barang (CRUD)
- Update harga barang sesuai pasar
- Verifikasi pengajuan (setujui/tolak dengan alasan)
- Generate invoice otomatis
- Laporan periodik

### Untuk Super Admin:
- Semua fitur admin
- Kelola user (tambah, edit, nonaktifkan)
- Monitoring log aktivitas
- Laporan menyeluruh

## 🚀 Cara Menjalankan

1. Pastikan semua langkah instalasi sudah dilakukan
2. Buka terminal dan navigasi ke folder project
3. Jalankan: `php artisan serve`
4. Buka browser dan akses: `http://localhost:8000`
5. Login dengan salah satu akun default di atas

## 📝 Catatan Pengembangan

Sistem ini sudah memiliki:
- ✅ Database schema lengkap
- ✅ Model dengan relationship
- ✅ Middleware autentikasi multi-role
- ✅ Seeder data contoh
- ✅ Migrations untuk semua tabel

Yang perlu dilengkapi:
- 🔄 Controller untuk masing-masing modul
- 🔄 Views (Blade templates)
- 🔄 Routes
- 🔄 Form validation
- 🔄 Export laporan

## 🤝 Kontribusi

Sistem ini siap untuk dikembangkan lebih lanjut dengan menambahkan:
- Interface user yang user-friendly
- Fitur export PDF untuk invoice
- Notifikasi email
- Dashboard dengan chart
- API untuk integrasi dengan sistem lain

---

**Dibuat untuk Dapur MBG** - Sistem Informasi Pengajuan Barang & Invoice
