<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pengajuan_id',
        'nomor_invoice',
        'tanggal_invoice',
        'total_harga',
        'status',
        'catatan',
        'issued_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_invoice' => 'date',
        'total_harga' => 'decimal:2',
    ];

    /**
     * Get the pengajuan that owns the invoice
     */
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    /**
     * Get the user who issued the invoice
     */
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Check if invoice is draft
     */
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    /**
     * Check if invoice is issued
     */
    public function isIssued()
    {
        return $this->status === 'issued';
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Issue invoice
     */
    public function issue()
    {
        $this->update(['status' => 'issued']);
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid()
    {
        $this->update(['status' => 'paid']);
    }

    /**
     * Generate invoice number
     */
    public static function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $lastInvoice = self::where('nomor_invoice', 'like', "{$prefix}-{$date}-%")->latest()->first();
        
        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->nomor_invoice, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return "{$prefix}-{$date}-{$newNumber}";
    }
}
