<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Invoice - Sistem Pengajuan Barang Dapur MBG</title>
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
                        <p class="text-green-200">Detail Invoice</p>
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
                    <a href="{{ route('staf.status') }}" class="text-gray-600 hover:text-green-600">Status Pengajuan</a>
                    <a href="{{ route('staf.invoice.show', $invoice) }}" class="text-green-600 font-semibold border-b-2 border-green-600 pb-2">Detail Invoice</a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <!-- Invoice Header -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-900">Detail Invoice</h2>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                @if($invoice->status === 'issued') bg-blue-100 text-blue-800
                                @elseif($invoice->status === 'paid') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                @if($invoice->status === 'issued') Diterbitkan
                                @elseif($invoice->status === 'paid') Dibayar
                                @else Dibatalkan @endif
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Invoice</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span class="font-medium text-gray-700">Nomor Invoice:</span>
                                        <span class="ml-2">{{ $invoice->nomor_invoice }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Nomor Pengajuan:</span>
                                        <span class="ml-2">{{ $invoice->pengajuan->nomor_pengajuan }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Tanggal Invoice:</span>
                                        <span class="ml-2">{{ $invoice->tanggal_invoice->format('d/m/Y') }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Dibuat oleh:</span>
                                        <span class="ml-2">{{ $invoice->issuedBy->name ?? 'Admin' }}</span>
                                    </div>
                                    @if($invoice->tanggal_pembayaran)
                                    <div>
                                        <span class="font-medium text-gray-700">Tanggal Pembayaran:</span>
                                        <span class="ml-2">{{ $invoice->tanggal_pembayaran->format('d/m/Y') }}</span>
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
                                            <span>{{ $invoice->pengajuan->items->count() }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Total Harga:</span>
                                            <span class="font-semibold">Rp {{ number_format($invoice->total_harga, 0, ',', '.') }}</span>
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
                                        @foreach($invoice->pengajuan->items as $item)
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

                        <!-- Catatan Pengajuan -->
                        @if($invoice->pengajuan->catatan)
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Catatan Pengajuan</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700">{{ $invoice->pengajuan->catatan }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Back Button -->
                <div class="flex justify-start">
                    <a href="{{ route('staf.status') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                        ← Kembali ke Status Pengajuan
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
