# Panduan Upload ke GitHub

## 📋 Persiapan Sebelum Upload

### 1. **File yang Harus Diabaikan (.gitignore)**
Pastikan file `.gitignore` sudah benar untuk Laravel:

```
/node_modules
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.env.production
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
/.idea
/.vscode
```

### 2. **Environment File**
Jangan upload file `.env` karena berisi konfigurasi sensitif.

## 🚀 Langkah-langkah Upload ke GitHub

### **Metode 1: Menggunakan Git Commands**

1. **Buka terminal di folder project:**
   ```bash
   cd c:\abi\mbg
   ```

2. **Inisialisasi Git repository:**
   ```bash
   git init
   ```

3. **Tambahkan semua file ke staging:**
   ```bash
   git add .
   ```

4. **Commit perubahan pertama:**
   ```bash
   git commit -m "Initial commit: Sistem Informasi Pengajuan Barang Dapur MBG"
   ```

5. **Buat repository baru di GitHub:**
   - Buka [github.com](https://github.com)
   - Klik "+" → "New repository"
   - Isi nama: `sistem-pengajuan-dapur-mbg`
   - Pilih "Public" atau "Private"
   - Jangan centang "Initialize with README"

6. **Hubungkan ke remote repository:**
   ```bash
   git remote add origin https://github.com/username/sistem-pengajuan-dapur-mbg.git
   ```

7. **Push ke GitHub:**
   ```bash
   git branch -M main
   git push -u origin main
   ```

### **Metode 2: Menggunakan GitHub Desktop**

1. **Download GitHub Desktop** dari [desktop.github.com](https://desktop.github.com/)

2. **Buka GitHub Desktop → File → Add Local Repository**

3. **Pilih folder:** `c:\abi\mbg`

4. **Commit perubahan:**
   - Summary: "Initial commit: Sistem Informasi Pengajuan Barang Dapur MBG"
   - Klik "Commit to main"

5. **Publish repository:**
   - Klik "Publish repository"
   - Isi nama: `sistem-pengajuan-dapur-mbg`
   - Pilih visibility (Public/Private)
   - Klik "Publish repository"

## 🔧 Setup Setelah Upload

### **Untuk Developer Lain yang Clone Project:**

1. **Clone repository:**
   ```bash
   git clone https://github.com/username/sistem-pengajuan-dapur-mbg.git
   cd sistem-pengajuan-dapur-mbg
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

4. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

5. **Setup database:**
   - Buat database SQLite: `database/database.sqlite`
   - Atau ubah ke MySQL di `.env`

6. **Run migrations dan seeders:**
   ```bash
   php artisan migrate --seed
   ```

7. **Build assets:**
   ```bash
   npm run build
   ```

8. **Jalankan server:**
   ```bash
   php artisan serve
   ```

## 📁 Struktur Project yang Diupload

```
sistem-pengajuan-dapur-mbg/
├── app/
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   └── views/
├── routes/
├── storage/
├── tests/
├── .env.example
├── .gitignore
├── artisan
├── composer.json
├── package.json
├── README.md
├── DOKUMENTASI_FINAL.md
├── INSTALASI.md
└── GITHUB_DEPLOYMENT.md
```

## ⚠️ Catatan Penting

1. **Jangan upload file `.env`** - berisi konfigurasi sensitif
2. **Pastikan semua dependency ada di `composer.json` dan `package.json`**
3. **Database SQLite sudah termasuk dalam repository** (`database/database.sqlite`)
4. **File dokumentasi lengkap sudah tersedia** untuk referensi developer lain

## 🔐 Default Login untuk Testing

Setelah clone dan setup, gunakan akun default:

- **Super Admin:** superadmin@mbg.com / password
- **Admin:** admin@mbg.com / password  
- **Staf:** staf@mbg.com / password

## 📞 Troubleshooting

Jika ada masalah saat push:
- Pastikan GitHub credentials sudah benar
- Cek koneksi internet
- Pastikan repository name tidak ada spasi

**Selamat! Project Laravel Anda sudah berhasil diupload ke GitHub 🎉**
