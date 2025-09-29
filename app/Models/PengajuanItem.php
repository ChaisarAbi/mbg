<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengajuanItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pengajuan_id',
        'barang_id',
        'jumlah',
        'harga_saat_ini',
        'subtotal',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_saat_ini' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the pengajuan that owns the item
     */
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    /**
     * Get the barang for this item
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Calculate subtotal automatically
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->subtotal = $model->jumlah * $model->harga_saat_ini;
        });
    }
}
