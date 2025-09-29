<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengajuan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'nomor_pengajuan',
        'tanggal_pengajuan',
        'keterangan',
        'status',
        'approved_by',
        'approved_at',
        'alasan_penolakan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user that owns the pengajuan
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who approved the pengajuan
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the pengajuan items
     */
    public function items()
    {
        return $this->hasMany(PengajuanItem::class);
    }

    /**
     * Get the invoice for this pengajuan
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Calculate total harga pengajuan
     */
    public function getTotalHargaAttribute()
    {
        return $this->items->sum('subtotal');
    }

    /**
     * Check if pengajuan is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if pengajuan is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if pengajuan is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Approve pengajuan
     */
    public function approve($approvedBy)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject pengajuan
     */
    public function reject($alasanPenolakan)
    {
        $this->update([
            'status' => 'rejected',
            'alasan_penolakan' => $alasanPenolakan,
        ]);
    }
}
