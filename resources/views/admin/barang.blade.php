<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Barang - Sistem Pengajuan Barang Dapur MBG</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">Sistem Pengajuan Barang Dapur MBG</h1>
                        <p class="text-blue-200">Kelola Data Barang</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span>Selamat datang, {{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-blue-700 hover:bg-blue-800 px-4 py-2 rounded-lg">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Navigation -->
        <nav class="bg-white shadow-md">
            <div class="container mx-auto px-4">
                <div class="flex space-x-8 py-3">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-blue-600">Dashboard</a>
                    <a href="{{ route('admin.barang') }}" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-2">Data Barang</a>
                    <a href="{{ route('admin.pengajuan') }}" class="text-gray-600 hover:text-blue-600">Pengajuan</a>
                    <a href="{{ route('admin.invoice') }}" class="text-gray-600 hover:text-blue-600">Invoice</a>
                    <a href="{{ route('admin.laporan') }}" class="text-gray-600 hover:text-blue-600">Laporan</a>
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('superadmin.dashboard') }}" class="text-red-600 hover:text-red-800">Super Admin</a>
                    @endif
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Data Barang</h2>
                <button onclick="toggleTambahBarang()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    + Tambah Barang
                </button>
            </div>

            <!-- Form Tambah Barang -->
            <div id="tambahBarangForm" class="bg-white rounded-lg shadow mb-6 hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Barang Baru</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.barang.store') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Barang</label>
                                <input type="text" name="nama_barang" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Masukkan nama barang" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <input type="text" name="deskripsi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Masukkan deskripsi" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Satuan</label>
                                <select name="satuan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="">Pilih satuan...</option>
                                    <option value="kg">kg</option>
                                    <option value="liter">liter</option>
                                    <option value="pcs">pcs</option>
                                    <option value="pack">pack</option>
                                    <option value="bungkus">bungkus</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Harga Awal</label>
                                <input type="number" name="harga_awal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Masukkan harga awal" required>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg w-full">
                                    Simpan Barang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Barang List -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Barang</h3>
                </div>
                <div class="p-6">
                    @if($barangs->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($barangs as $barang)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $barang->nama_barang }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $barang->deskripsi }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $barang->satuan }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($barang->harga_saat_ini, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($barang->status_aktif)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                            <button class="text-red-600 hover:text-red-900">Hapus</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Belum ada data barang.</p>
                    @endif
                </div>
            </div>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="container mx-auto px-4 py-2">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Sukses!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="container mx-auto px-4 py-2">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
        @endif

            <!-- Update Harga Section -->
            <div class="bg-white rounded-lg shadow mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Update Harga Barang</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.barang.update-harga') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pilih Barang</label>
                                <select name="barang_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="">Pilih barang...</option>
                                    @foreach($barangs as $barang)
                                        <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Harga Baru</label>
                                <input type="number" name="harga_baru" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Masukkan harga baru" required>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg w-full">
                                    Update Harga
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleTambahBarang() {
            const form = document.getElementById('tambahBarangForm');
            form.classList.toggle('hidden');
        }
    </script>
</body>
</html>
