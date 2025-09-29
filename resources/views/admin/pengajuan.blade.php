<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengajuan - Sistem Pengajuan Barang Dapur MBG</title>
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
                        <p class="text-blue-200">Kelola Pengajuan Barang</p>
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
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Daftar Pengajuan</h2>
                <div class="flex space-x-4">
                    <select class="rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                    <input type="date" class="rounded-md border-gray-300 shadow-sm">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Filter
                    </button>
                </div>
            </div>

            <!-- Pengajuan List -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Semua Pengajuan</h3>
                </div>
                <div class="p-6">
                    @if($pengajuans->count() > 0)
                        <div class="space-y-4">
                            @foreach($pengajuans as $pengajuan)
                            <div class="border rounded-lg p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $pengajuan->nomor_pengajuan }}</h4>
                                        <p class="text-sm text-gray-500">
                                            Oleh: {{ $pengajuan->user->name }} | 
                                            Tanggal: {{ $pengajuan->tanggal_pengajuan->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
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

                                <!-- Detail Items -->
                                <div class="mb-3">
                                    <h5 class="font-medium text-gray-700 mb-2">Detail Barang:</h5>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-3 py-2 text-left">Nama Barang</th>
                                                    <th class="px-3 py-2 text-left">Jumlah</th>
                                                    <th class="px-3 py-2 text-left">Harga Satuan</th>
                                                    <th class="px-3 py-2 text-left">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pengajuan->items as $item)
                                                <tr>
                                                    <td class="px-3 py-2">{{ $item->barang->nama_barang }}</td>
                                                    <td class="px-3 py-2">{{ $item->jumlah }} {{ $item->barang->satuan }}</td>
                                                    <td class="px-3 py-2">Rp {{ number_format($item->harga_saat_ini, 0, ',', '.') }}</td>
                                                    <td class="px-3 py-2">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Total dan Informasi -->
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-gray-600">
                                        <p><strong>Total: Rp {{ number_format($pengajuan->total_harga, 0, ',', '.') }}</strong></p>
                                        @if($pengajuan->catatan)
                                            <p class="mt-1"><strong>Catatan:</strong> {{ $pengajuan->catatan }}</p>
                                        @endif
                                    </div>
                                    <div class="flex space-x-2">
                                        @if($pengajuan->status === 'pending')
                                            <form method="POST" action="{{ route('admin.pengajuan.approve', $pengajuan) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                                    Setujui
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.pengajuan.reject', $pengajuan) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                                                    Tolak
                                                </button>
                                            </form>
                                        @elseif($pengajuan->status === 'approved' && !$pengajuan->invoice)
                                            <form method="POST" action="{{ route('admin.invoice.generate', $pengajuan) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                                    Buat Invoice
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.pengajuan.show', $pengajuan) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                                            Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pengajuan</h3>
                            <p class="mt-1 text-sm text-gray-500">Tidak ada pengajuan yang perlu diverifikasi saat ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>
