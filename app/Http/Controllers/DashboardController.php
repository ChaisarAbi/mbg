<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    /**
     * Redirect to appropriate dashboard based on user role
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('staf.dashboard');
        }
    }

    /**
     * Admin dashboard
     */
    public function admin()
    {
        $stats = [
            'total_barang' => Barang::count(),
            'total_pengajuan' => Pengajuan::count(),
            'pengajuan_pending' => Pengajuan::where('status', 'pending')->count(),
            'pengajuan_approved' => Pengajuan::where('status', 'approved')->count(),
            'total_invoice' => Invoice::count(),
        ];

        $recent_pengajuan = Pengajuan::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_pengajuan'));
    }

    /**
     * Super Admin dashboard
     */
    public function superAdmin()
    {
        $stats = [
            'total_users' => User::count(),
            'total_barang' => Barang::count(),
            'total_pengajuan' => Pengajuan::count(),
            'total_invoice' => Invoice::count(),
            'active_users' => User::where('is_active', true)->count(),
        ];

        $recent_activities = Pengajuan::with(['user', 'approvedBy'])->latest()->take(10)->get();

        return view('superadmin.dashboard', compact('stats', 'recent_activities'));
    }

    /**
     * Staf dashboard
     */
    public function staf()
    {
        $user = auth()->user();
        
        $stats = [
            'total_pengajuan' => $user->pengajuans()->count(),
            'pengajuan_pending' => $user->pengajuans()->where('status', 'pending')->count(),
            'pengajuan_approved' => $user->pengajuans()->where('status', 'approved')->count(),
            'pengajuan_rejected' => $user->pengajuans()->where('status', 'rejected')->count(),
        ];

        $recent_pengajuan = $user->pengajuans()->with('items.barang')->latest()->take(5)->get();

        return view('staf.dashboard', compact('stats', 'recent_pengajuan'));
    }

    /**
     * Admin - Laporan
     */
    public function laporan(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $type = $request->get('type', 'pengajuan');

        // Build query for pengajuan with flexible filtering
        $pengajuanQuery = Pengajuan::with(['user', 'items.barang', 'invoice']);

        // Apply date filter only if dates are provided
        if ($request->filled('start_date') || $request->filled('end_date')) {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $pengajuanQuery->whereBetween('tanggal_pengajuan', [$startDate, $endDate]);
            } elseif ($request->filled('start_date')) {
                $pengajuanQuery->where('tanggal_pengajuan', '>=', $startDate);
            } elseif ($request->filled('end_date')) {
                $pengajuanQuery->where('tanggal_pengajuan', '<=', $endDate);
            }
        }

        $pengajuans = $pengajuanQuery->latest()->get();

        // Build query for invoices with flexible filtering
        $invoiceQuery = Invoice::with(['pengajuan.user', 'issuedBy']);

        // Apply date filter only if dates are provided
        if ($request->filled('start_date') || $request->filled('end_date')) {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $invoiceQuery->whereBetween('tanggal_invoice', [$startDate, $endDate]);
            } elseif ($request->filled('start_date')) {
                $invoiceQuery->where('tanggal_invoice', '>=', $startDate);
            } elseif ($request->filled('end_date')) {
                $invoiceQuery->where('tanggal_invoice', '<=', $endDate);
            }
        }

        $invoices = $invoiceQuery->latest()->get();

        $stats = [
            'total_pengajuan' => $pengajuans->count(),
            'pengajuan_pending' => $pengajuans->where('status', 'pending')->count(),
            'pengajuan_approved' => $pengajuans->where('status', 'approved')->count(),
            'pengajuan_rejected' => $pengajuans->where('status', 'rejected')->count(),
            'total_invoice' => $invoices->count(),
            'invoice_issued' => $invoices->where('status', 'issued')->count(),
            'invoice_paid' => $invoices->where('status', 'paid')->count(),
            'total_revenue' => $invoices->where('status', 'paid')->sum('total_harga'),
        ];

        if ($request->has('export') && $request->export == 'pdf') {
            return $this->exportPdf($pengajuans, $invoices, $startDate, $endDate, $type);
        }

        if ($request->has('export') && $request->export == 'excel') {
            return $this->exportExcel($pengajuans, $invoices, $startDate, $endDate, $type);
        }

        return view('admin.laporan', compact('stats', 'pengajuans', 'invoices', 'startDate', 'endDate', 'type'));
    }

    /**
     * Export PDF
     */
    private function exportPdf($pengajuans, $invoices, $startDate, $endDate, $type)
    {
        $filename = "laporan_{$type}_" . date('Y-m-d') . ".pdf";
        
        // Prepare data for PDF
        $data = [
            'type' => $type,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pengajuans' => $pengajuans,
            'invoices' => $invoices,
            'tanggalCetak' => date('d/m/Y H:i')
        ];

        // Generate PDF using dompdf
        $pdf = Pdf::loadView('admin.laporan-pdf', $data);
        
        return $pdf->download($filename);
    }

    /**
     * Export Excel
     */
    private function exportExcel($pengajuans, $invoices, $startDate, $endDate, $type)
    {
        $filename = "laporan_{$type}_" . date('Y-m-d') . ".csv";
        
        // Create detailed CSV content
        $content = "";
        
        if ($type == 'pengajuan') {
            // Header informasi
            $content .= "LAPORAN PENGAJUAN BARANG DAPUR MBG\n";
            $content .= "Periode: {$startDate} sampai {$endDate}\n";
            $content .= "Tanggal Cetak: " . date('d/m/Y H:i') . "\n";
            $content .= "Total Pengajuan: " . $pengajuans->count() . "\n\n";
            
            // Header tabel utama
            $content .= "No,Nomor Pengajuan,Nama Staf,Tanggal Pengajuan,Status,Total Harga,Catatan\n";
            
            // Data utama
            foreach ($pengajuans as $index => $pengajuan) {
                $content .= ($index + 1) . ",\"{$pengajuan->nomor_pengajuan}\",\"{$pengajuan->user->name}\",\"{$pengajuan->tanggal_pengajuan->format('d/m/Y')}\",\"{$pengajuan->status}\",\"" . 
                           number_format($pengajuan->total_harga, 0, ',', '.') . "\",\"{$pengajuan->catatan}\"\n";
            }
            
            // Detail barang untuk setiap pengajuan
            $content .= "\n\nDETAIL BARANG:\n";
            $content .= "Nomor Pengajuan,Nama Barang,Jumlah,Satuan,Harga Satuan,Subtotal\n";
            foreach ($pengajuans as $pengajuan) {
                foreach ($pengajuan->items as $item) {
                    $content .= "\"{$pengajuan->nomor_pengajuan}\",\"{$item->barang->nama_barang}\",\"{$item->jumlah}\",\"{$item->barang->satuan}\",\"" . 
                               number_format($item->harga_saat_ini, 0, ',', '.') . "\",\"" . 
                               number_format($item->subtotal, 0, ',', '.') . "\"\n";
                }
            }
        } else {
            // Header informasi
            $content .= "LAPORAN INVOICE DAPUR MBG\n";
            $content .= "Periode: {$startDate} sampai {$endDate}\n";
            $content .= "Tanggal Cetak: " . date('d/m/Y H:i') . "\n";
            $content .= "Total Invoice: " . $invoices->count() . "\n\n";
            
            // Header tabel utama
            $content .= "No,Nomor Invoice,Nomor Pengajuan,Nama Staf,Tanggal Invoice,Status,Total Harga,Dibuat Oleh\n";
            
            // Data utama
            foreach ($invoices as $index => $invoice) {
                $nomorPengajuan = $invoice->pengajuan->nomor_pengajuan ?? 'N/A';
                $namaStaf = $invoice->pengajuan->user->name ?? 'N/A';
                $dibuatOleh = $invoice->issuedBy->name ?? 'N/A';
                
                $content .= ($index + 1) . ",\"{$invoice->nomor_invoice}\",\"{$nomorPengajuan}\",\"{$namaStaf}\",\"{$invoice->tanggal_invoice->format('d/m/Y')}\",\"{$invoice->status}\",\"" . 
                           number_format($invoice->total_harga, 0, ',', '.') . "\",\"{$dibuatOleh}\"\n";
            }
            
            // Detail barang untuk setiap invoice
            $content .= "\n\nDETAIL BARANG:\n";
            $content .= "Nomor Invoice,Nomor Pengajuan,Nama Barang,Jumlah,Satuan,Harga Satuan,Subtotal\n";
            foreach ($invoices as $invoice) {
                if ($invoice->pengajuan && $invoice->pengajuan->items) {
                    foreach ($invoice->pengajuan->items as $item) {
                        $nomorPengajuan = $invoice->pengajuan->nomor_pengajuan ?? 'N/A';
                        $namaBarang = $item->barang->nama_barang ?? 'N/A';
                        $satuan = $item->barang->satuan ?? '';
                        
                        $content .= "\"{$invoice->nomor_invoice}\",\"{$nomorPengajuan}\",\"{$namaBarang}\",\"{$item->jumlah}\",\"{$satuan}\",\"" . 
                                   number_format($item->harga_saat_ini, 0, ',', '.') . "\",\"" . 
                                   number_format($item->subtotal, 0, ',', '.') . "\"\n";
                    }
                }
            }
        }

        return response($content)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Super Admin - Kelola users
     */
    public function users()
    {
        $users = User::all();
        return view('superadmin.users', compact('users'));
    }
}
