<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with('pengajuan.user')->latest()->get();
        return view('admin.invoice', compact('invoices'));
    }

    /**
     * Generate invoice for approved pengajuan
     */
    public function generate(Pengajuan $pengajuan)
    {
        // Check if pengajuan is approved
        if ($pengajuan->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya pengajuan yang disetujui yang dapat dibuatkan invoice!');
        }

        // Check if invoice already exists
        if ($pengajuan->invoice) {
            return redirect()->back()->with('error', 'Invoice untuk pengajuan ini sudah ada!');
        }

        try {
            DB::beginTransaction();

            // Generate invoice number
            $lastInvoice = Invoice::latest()->first();
            $nomorInvoice = 'INV-' . date('Ymd') . '-' . str_pad(($lastInvoice ? $lastInvoice->id + 1 : 1), 4, '0', STR_PAD_LEFT);

            // Create invoice
            $invoice = Invoice::create([
                'nomor_invoice' => $nomorInvoice,
                'pengajuan_id' => $pengajuan->id,
                'tanggal_invoice' => now(),
                'total_harga' => $pengajuan->total_harga,
                'status' => 'issued',
                'issued_by' => auth()->id()
            ]);

            DB::commit();

            return redirect()->route('admin.invoice')->with('success', 'Invoice berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Mark invoice as paid
     */
    public function markPaid(Invoice $invoice)
    {
        try {
            $invoice->update([
                'status' => 'paid',
                'tanggal_pembayaran' => now()
            ]);

            return redirect()->back()->with('success', 'Invoice berhasil ditandai sebagai dibayar!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show invoice detail
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['pengajuan.user', 'pengajuan.items.barang', 'issuedBy']);
        return view('admin.invoice-detail', compact('invoice'));
    }

    /**
     * Show invoice detail for staf (only their own invoices)
     */
    public function showForStaf(Invoice $invoice)
    {
        // Check if the invoice belongs to the current staf
        if ($invoice->pengajuan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $invoice->load(['pengajuan.user', 'pengajuan.items.barang', 'issuedBy']);
        return view('staf.invoice-detail', compact('invoice'));
    }

    /**
     * Print invoice
     */
    public function print(Invoice $invoice)
    {
        $invoice->load(['pengajuan.user', 'pengajuan.items.barang', 'issuedBy']);
        return view('admin.invoice-print', compact('invoice'));
    }

    /**
     * Download invoice as PDF
     */
    public function download(Invoice $invoice)
    {
        // This would typically generate a PDF
        // For now, redirect to print view
        return redirect()->route('admin.invoice.print', $invoice);
    }

    /**
     * Cancel invoice
     */
    public function cancel(Invoice $invoice)
    {
        try {
            $invoice->update([
                'status' => 'cancelled'
            ]);

            return redirect()->back()->with('success', 'Invoice berhasil dibatalkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
