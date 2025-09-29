<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Barang;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangs = [
            [
                'nama_barang' => 'Beras Premium',
                'deskripsi' => 'Beras kualitas premium untuk dapur MBG',
                'satuan' => 'kg',
                'harga_saat_ini' => 15000,
                'status_aktif' => true,
            ],
            [
                'nama_barang' => 'Minyak Goreng',
                'deskripsi' => 'Minyak goreng kemasan 2 liter',
                'satuan' => 'liter',
                'harga_saat_ini' => 25000,
                'status_aktif' => true,
            ],
            [
                'nama_barang' => 'Gula Pasir',
                'deskripsi' => 'Gula pasir putih kemasan 1 kg',
                'satuan' => 'kg',
                'harga_saat_ini' => 12000,
                'status_aktif' => true,
            ],
            [
                'nama_barang' => 'Garam Halus',
                'deskripsi' => 'Garam halus kemasan 1 kg',
                'satuan' => 'kg',
                'harga_saat_ini' => 8000,
                'status_aktif' => true,
            ],
            [
                'nama_barang' => 'Telur Ayam',
                'deskripsi' => 'Telur ayam segar per kg',
                'satuan' => 'kg',
                'harga_saat_ini' => 28000,
                'status_aktif' => true,
            ],
            [
                'nama_barang' => 'Ayam Potong',
                'deskripsi' => 'Ayam potong segar per kg',
                'satuan' => 'kg',
                'harga_saat_ini' => 35000,
                'status_aktif' => true,
            ],
            [
                'nama_barang' => 'Ikan Segar',
                'deskripsi' => 'Ikan segar berbagai jenis per kg',
                'satuan' => 'kg',
                'harga_saat_ini' => 40000,
                'status_aktif' => true,
            ],
            [
                'nama_barang' => 'Sayuran Segar',
                'deskripsi' => 'Berbagai jenis sayuran segar',
                'satuan' => 'ikat',
                'harga_saat_ini' => 5000,
                'status_aktif' => true,
            ],
            [
                'nama_barang' => 'Bumbu Dapur',
                'deskripsi' => 'Bumbu dapur lengkap (bawang, cabe, dll)',
                'satuan' => 'paket',
                'harga_saat_ini' => 15000,
                'status_aktif' => true,
            ],
            [
                'nama_barang' => 'Gas LPG 3kg',
                'deskripsi' => 'Tabung gas LPG 3kg untuk dapur',
                'satuan' => 'tabung',
                'harga_saat_ini' => 25000,
                'status_aktif' => true,
            ],
        ];

        foreach ($barangs as $barang) {
            Barang::updateOrCreate(
                ['nama_barang' => $barang['nama_barang']],
                $barang
            );
        }
    }
}
