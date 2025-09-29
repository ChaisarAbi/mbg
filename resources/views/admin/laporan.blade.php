<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Sistem Pengajuan Barang Dapur MBG</title>
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
                        <p class="text-blue-200">Laporan Sistem</p>
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
                    <a href="{{ route('admin.invoice') }}" class="text-gray-600 hover:text-blue-600">Invoice</a>
                    <a href="{{ route('admin.laporan') }}" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-2">Laporan</a>
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

        @if(session('info'))
        <div class="container mx-auto px-4 py-2">
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Info!</strong>
                <span class="block sm:inline">{{ session('info') }}</span>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <!-- Filter Section -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Laporan</h3>
                    <form method="GET" action="{{ route('admin.laporan') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis Laporan</label>
                            <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="pengajuan" {{ $type == 'pengajuan' ? 'selected' : '' }}>Laporan Pengajuan</option>
                                <option value="invoice" {{ $type == 'invoice' ? 'selected' : '' }}>Laporan Invoice</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg w-full">
                                Generate Laporan
                            </button>
                            <a href="{{ route('admin.laporan') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistik Section -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Pengajuan</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_pengajuan'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pengajuan Disetujui</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['pengajuan_approved'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Invoice</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_invoice'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Laporan Detail -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Ringkasan Laporan</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Chart Placeholder -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Statistik Pengajuan per Bulan</h4>
                            <div class="h-64 flex items-center justify-center">
                                <p class="text-gray-500">Chart akan ditampilkan di sini</p>
                            </div>
                        </div>

                        <!-- Top Barang -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Barang Paling Sering Diajukan</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm">Beras</span>
                                    <span class="text-sm font-semibold">25 kali</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm">Minyak Goreng</span>
                                    <span class="text-sm font-semibold">18 kali</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm">Gula</span>
                                    <span class="text-sm font-semibold">15 kali</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm">Telur</span>
                                    <span class="text-sm font-semibold">12 kali</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm">Ayam</span>
                                    <span class="text-sm font-semibold">10 kali</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Options -->
                    <div class="mt-6 flex justify-end space-x-4">
                        <form method="GET" action="{{ route('admin.laporan') }}" class="inline">
                            <input type="hidden" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" name="end_date" value="{{ $endDate }}">
                            <input type="hidden" name="type" value="{{ $type }}">
                            <input type="hidden" name="export" value="pdf">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                                Export PDF
                            </button>
                        </form>
                        <form method="GET" action="{{ route('admin.laporan') }}" class="inline">
                            <input type="hidden" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" name="end_date" value="{{ $endDate }}">
                            <input type="hidden" name="type" value="{{ $type }}">
                            <input type="hidden" name="export" value="excel">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                                Export Excel
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
