<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pengajuan - Sistem Pengajuan Barang Dapur MBG</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-green-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">Sistem Pengajuan Barang Dapur MBG</h1>
                        <p class="text-green-200">Status Pengajuan</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span>Selamat datang, {{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-green-700 hover:bg-green-800 px-4 py-2 rounded-lg">
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
                    <a href="{{ route('staf.dashboard') }}" class="text-gray-600 hover:text-green-600">Dashboard</a>
                    <a href="{{ route('staf.pengajuan') }}" class="text-gray-600 hover:text-green-600">Buat Pengajuan</a>
                    <a href="{{ route('staf.status') }}" class="text-green-600 font-semibold border-b-2 border-green-600 pb-2">Status Pengajuan</a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <div class="max-w-6xl mx-auto">
                <!-- Filter Section -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Pengajuan</h3>
                        <form method="GET" action="{{ route('staf.status') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div class="flex items-end space-x-2">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg w-full">
                                    Filter
                                </button>
                                <a href="{{ route('staf.status') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Pengajuan List -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Riwayat Pengajuan</h2>
                    </div>
                    <div class="p-6">
                        @if($pengajuans->count() > 0)
                            <div class="space-y-4">
                                @foreach($pengajuans as $pengajuan)
                                <div class="border rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $pengajuan->nomor_pengajuan }}</h4>
                                            <p class="text-sm text-gray-500">Diajukan pada: {{ $pengajuan->tanggal_pengajuan->format('d/m/Y H:i') }}</p>
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
                                            @if($pengajuan->invoice)
                                                <span class="ml-2 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-semibold">
                                                    Invoice: {{ $pengajuan->invoice->nomor_invoice }}
                                                </span>
                                            @endif
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
                                            @if($pengajuan->status !== 'pending')
                                                <p class="mt-1">
                                                    <strong>Disetujui/Ditolak oleh:</strong> {{ $pengajuan->approvedBy->name ?? '-' }}
                                                    @if($pengajuan->tanggal_persetujuan)
                                                        pada {{ $pengajuan->tanggal_persetujuan->format('d/m/Y H:i') }}
                                                    @endif
                                                </p>
                                                @if($pengajuan->alasan_penolakan)
                                                    <p class="mt-1"><strong>Alasan Penolakan:</strong> {{ $pengajuan->alasan_penolakan }}</p>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            @if($pengajuan->invoice && $pengajuan->invoice->isIssued())
                                                <a href="{{ route('staf.invoice.show', $pengajuan->invoice) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                                    Lihat Invoice
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6 flex justify-center">
                                <nav class="inline-flex rounded-md shadow">
                                    <a href="#" class="px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Previous</a>
                                    <a href="#" class="px-3 py-2 border-t border-b border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">1</a>
                                    <a href="#" class="px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">2</a>
                                    <a href="#" class="px-3 py-2 border-t border-b border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">3</a>
                                    <a href="#" class="px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Next</a>
                                </nav>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pengajuan</h3>
                                <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat pengajuan barang pertama Anda.</p>
                                <div class="mt-6">
                                    <a href="{{ route('staf.pengajuan') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                        Buat Pengajuan Baru
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
