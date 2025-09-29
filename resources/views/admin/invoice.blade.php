<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Invoice - Sistem Pengajuan Barang Dapur MBG</title>
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
                        <p class="text-blue-200">Kelola Invoice</p>
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
                    <a href="{{ route('admin.pengajuan') }}" class="text-gray-600 hover:text-blue-600">Pengajuan</a>
                    <a href="{{ route('admin.invoice') }}" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-2">Invoice</a>
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
                <h2 class="text-2xl font-bold text-gray-900">Daftar Invoice</h2>
                <div class="flex space-x-4">
                    <select class="rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="issued">Issued</option>
                        <option value="paid">Paid</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <input type="date" class="rounded-md border-gray-300 shadow-sm">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Filter
                    </button>
                </div>
            </div>

            <!-- Invoice List -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Semua Invoice</h3>
                </div>
                <div class="p-6">
                    @if($invoices->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Invoice</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengajuan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($invoices as $invoice)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invoice->nomor_invoice }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $invoice->pengajuan->nomor_pengajuan }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->tanggal_invoice->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($invoice->total_harga, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($invoice->status === 'issued') bg-yellow-100 text-yellow-800
                                                @elseif($invoice->status === 'paid') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800 @endif">
                                                @if($invoice->status === 'issued') Issued
                                                @elseif($invoice->status === 'paid') Paid
                                                @else Cancelled @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.invoice.show', $invoice) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                                            <a href="{{ route('admin.invoice.print', $invoice) }}" class="text-green-600 hover:text-green-900 mr-3">Print</a>
                                            @if($invoice->status === 'issued')
                                                <form method="POST" action="{{ route('admin.invoice.mark-paid', $invoice) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900 mr-3">Mark Paid</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.invoice.cancel', $invoice) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Cancel</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada invoice</h3>
                            <p class="mt-1 text-sm text-gray-500">Invoice akan muncul setelah pengajuan disetujui dan dibuatkan invoice.</p>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>
