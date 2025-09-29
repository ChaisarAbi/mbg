<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan - Sistem Pengajuan Barang Dapur MBG</title>
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
                        <p class="text-blue-200">Detail Pengajuan</p>
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
                    <a href="{{ route('admin.barang') }}" class="text-gray-600 hover:text-blue-600">Data Barang</a>
                    <a href="{{ route('admin.pengajuan') }}" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-2">Pengajuan</a>
                    <a href="{{ route('admin.invoice') }}" class="text-gray-600 hover:text-blue-600">Invoice</a>
                    <a href="{{ route('admin.laporan') }}" class="text-gray-600 hover:text-blue-600">Laporan</a>
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('superadmin.dashboard') }}" class="text-red-600 hover:text-red-800">Super Admin</a>
                    @endif
                </div>
            </div>
        </nav>

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

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <!-- Header Section -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-900">Detail Pengajuan</h2>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                @if($pengajuan->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($pengajuan->status === 'approved') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                @if($pengajuan->status === 'pending') Menunggu Persetujuan
                                @elseif($pengajuan->status === 'approved') Disetujui
                                @else Ditolak @endif
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pengajuan</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span class="font-medium text-gray-700">Nomor Pengajuan:</span>
                                        <span class="ml-2">{{ $pengajuan->nomor_pengajuan }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Diajukan oleh:</span>
                                        <span class="ml-2">{{ $pengajuan->user->name }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Tanggal Pengajuan:</span>
                                        <span class="ml-2">{{ $pengajuan->tanggal_pengajuan->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($pengajuan->status !== 'pending')
                                    <div>
                                        <span class="font-medium text-gray-700">Status:</span>
                                        <span class="ml-2">
                                            @if($pengajuan->status === 'approved')
                                                Disetujui oleh {{ $pengajuan->approvedBy->name ?? 'Admin' }}
                                            @else
                                                Ditolak
                                            @endif
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span>Total Item:</span>
                                            <span>{{ $pengajuan->items->count() }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Total Harga:</span>
                                            <span class="font-semibold">Rp {{ number_format($pengajuan->total_harga, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Barang -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Barang</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pengajuan->items as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->barang->nama_barang }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->jumlah }} {{ $item->barang->satuan }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($item->harga_saat_ini, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Catatan -->
                        @if($pengajuan->catatan)
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Catatan</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700">{{ $pengajuan->catatan }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        @if($pengajuan->status === 'pending')
                        <div class="flex justify-end space-x-4">
                            <form method="POST" action="{{ route('admin.pengajuan.approve', $pengajuan) }}">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                                    Setujui Pengajuan
                                </button>
                            </form>
                            <button type="button" onclick="openRejectModal()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
                                Tolak Pengajuan
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Reject Modal -->
                <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3 text-center">
                            <h3 class="text-lg font-medium text-gray-900">Tolak Pengajuan</h3>
                            <form id="rejectForm" method="POST" action="{{ route('admin.pengajuan.reject', $pengajuan) }}" class="mt-4 space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 text-left">Alasan Penolakan</label>
                                    <textarea name="alasan_penolakan" id="alasan_penolakan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="4" placeholder="Masukkan alasan penolakan..." required></textarea>
                                </div>
                                <div class="flex justify-end space-x-4 mt-6">
                                    <button type="button" onclick="closeRejectModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                                        Batal
                                    </button>
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                        Tolak Pengajuan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="flex justify-start">
                    <a href="{{ route('admin.pengajuan') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                        ← Kembali ke Daftar Pengajuan
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        function openRejectModal() {
            document.getElementById('rejectModal').style.display = 'block';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
            document.getElementById('alasan_penolakan').value = '';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('rejectModal');
            if (event.target === modal) {
                closeRejectModal();
            }
        }

        // Handle form submission
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            const alasan = document.getElementById('alasan_penolakan').value.trim();
            if (!alasan) {
                e.preventDefault();
                alert('Alasan penolakan harus diisi!');
                return false;
            }
        });
    </script>
</body>
</html>
