<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama_barang',
        'deskripsi',
        'satuan',
        'harga_saat_ini',
        'status_aktif',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'harga_saat_ini' => 'decimal:2',
        'status_aktif' => 'boolean',
    ];

    /**
     * Get pengajuan items for this barang
     */
    public function pengajuanItems()
    {
        return $this->hasMany(PengajuanItem::class);
    }

    /**
     * Scope active barang
     */
    public function scopeActive($query)
    {
        return $query->where('status_aktif', true);
    }

    /**
     * Update harga barang
     */
    public function updateHarga($hargaBaru)
    {
        $this->update(['harga_saat_ini' => $hargaBaru]);
        return $this;
    }
}
