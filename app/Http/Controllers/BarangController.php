<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangs = Barang::active()->get();
        return view('admin.barang', compact('barangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.barang-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255|unique:barangs,nama_barang',
            'deskripsi' => 'required|string|max:500',
            'satuan' => 'required|string|max:50',
            'harga_awal' => 'required|numeric|min:0'
        ]);

        try {
            Barang::create([
                'nama_barang' => $request->nama_barang,
                'deskripsi' => $request->deskripsi,
                'satuan' => $request->satuan,
                'harga_saat_ini' => $request->harga_awal,
                'status_aktif' => true
            ]);
            return redirect()->route('admin.barang')->with('success', 'Barang berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {
        return view('admin.barang-show', compact('barang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        return view('admin.barang-edit', compact('barang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255|unique:barangs,nama_barang,' . $barang->id,
            'deskripsi' => 'required|string|max:500',
            'satuan' => 'required|string|max:50',
            'harga_saat_ini' => 'required|numeric|min:0',
            'status_aktif' => 'required|boolean'
        ]);

        try {
            $barang->update($request->all());
            return redirect()->route('admin.barang')->with('success', 'Barang berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    {
        try {
            // Check if barang is used in any pengajuan
            if ($barang->pengajuanItems()->exists()) {
                return redirect()->back()->with('error', 'Barang tidak dapat dihapus karena sudah digunakan dalam pengajuan!');
            }

            $barang->delete();
            return redirect()->route('admin.barang')->with('success', 'Barang berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update harga barang
     */
    public function updateHarga(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'harga_baru' => 'required|numeric|min:0'
        ]);

        try {
            $barang = Barang::find($request->barang_id);
            $harga_lama = $barang->harga_saat_ini;
            
            $barang->update([
                'harga_saat_ini' => $request->harga_baru
            ]);

            return redirect()->back()->with('success', 
                "Harga {$barang->nama_barang} berhasil diupdate dari Rp " . 
                number_format($harga_lama, 0, ',', '.') . 
                " menjadi Rp " . number_format($request->harga_baru, 0, ',', '.')
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Search barang
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        
        $barangs = Barang::when($search, function($query) use ($search) {
            return $query->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
        })->active()->get();

        return view('admin.barang', compact('barangs', 'search'));
    }

    /**
     * Toggle status barang
     */
    public function toggleStatus(Barang $barang)
    {
        try {
            $barang->update([
                'status_aktif' => !$barang->status_aktif
            ]);

            $status = $barang->status_aktif ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()->with('success', "Barang berhasil {$status}!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
