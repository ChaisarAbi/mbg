<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    /**
     * Display a listing of the resource for admin.
     */
    public function index()
    {
        $pengajuans = Pengajuan::with(['user', 'items.barang'])->latest()->get();
        return view('admin.pengajuan', compact('pengajuans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barangs = Barang::active()->get();
        return view('staf.pengajuan', compact('barangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|array',
            'barang_id.*' => 'exists:barangs,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|numeric|min:1',
            'catatan' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Generate nomor pengajuan
            $lastPengajuan = Pengajuan::latest()->first();
            $nomorPengajuan = 'PJN-' . date('Ymd') . '-' . str_pad(($lastPengajuan ? $lastPengajuan->id + 1 : 1), 4, '0', STR_PAD_LEFT);

            // Create pengajuan
            $pengajuan = Pengajuan::create([
                'nomor_pengajuan' => $nomorPengajuan,
                'user_id' => auth()->id(),
                'tanggal_pengajuan' => now(),
                'catatan' => $request->catatan,
                'status' => 'pending',
                'total_harga' => 0
            ]);

            $totalHarga = 0;

            // Create pengajuan items
            foreach ($request->barang_id as $index => $barangId) {
                $barang = Barang::find($barangId);
                $jumlah = $request->jumlah[$index];
                $subtotal = $barang->harga_saat_ini * $jumlah;

                PengajuanItem::create([
                    'pengajuan_id' => $pengajuan->id,
                    'barang_id' => $barangId,
                    'jumlah' => $jumlah,
                    'harga_saat_ini' => $barang->harga_saat_ini,
                    'subtotal' => $subtotal
                ]);

                $totalHarga += $subtotal;
            }

            // Update total harga
            $pengajuan->update(['total_harga' => $totalHarga]);

            DB::commit();

            return redirect()->route('staf.status')->with('success', 'Pengajuan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengajuan $pengajuan)
    {
        $pengajuan->load(['user', 'items.barang', 'approvedBy', 'invoice']);
        return view('admin.pengajuan-detail', compact('pengajuan'));
    }

    /**
     * Approve pengajuan
     */
    public function approve(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'catatan_approval' => 'nullable|string|max:500'
        ]);

        $pengajuan->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'tanggal_persetujuan' => now(),
            'catatan_approval' => $request->catatan_approval
        ]);

        return redirect()->back()->with('success', 'Pengajuan berhasil disetujui!');
    }

    /**
     * Reject pengajuan
     */
    public function reject(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500'
        ]);

        $pengajuan->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'tanggal_persetujuan' => now(),
            'alasan_penolakan' => $request->alasan_penolakan
        ]);

        return redirect()->back()->with('success', 'Pengajuan berhasil ditolak!');
    }

    /**
     * Get pengajuan for staf
     */
    public function stafPengajuan()
    {
        $barangs = Barang::active()->get();
        return view('staf.pengajuan', compact('barangs'));
    }

    /**
     * Get status for staf
     */
    public function stafStatus(Request $request)
    {
        $user = auth()->user();
        $pengajuans = $user->pengajuans()->with(['items.barang', 'invoice', 'approvedBy']);
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $pengajuans->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->start_date != '') {
            $pengajuans->whereDate('tanggal_pengajuan', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date != '') {
            $pengajuans->whereDate('tanggal_pengajuan', '<=', $request->end_date);
        }
        
        $pengajuans = $pengajuans->latest()->get();
        return view('staf.status', compact('pengajuans'));
    }
}
