<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Pengajuan Barang - Sistem Pengajuan Barang Dapur MBG</title>
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
                        <p class="text-green-200">Buat Pengajuan Barang</p>
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
                    <a href="{{ route('staf.pengajuan') }}" class="text-green-600 font-semibold border-b-2 border-green-600 pb-2">Buat Pengajuan</a>
                    <a href="{{ route('staf.status') }}" class="text-gray-600 hover:text-green-600">Status Pengajuan</a>
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
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Form Pengajuan Barang</h2>
                    </div>
                    <div class="p-6">
                        <form id="pengajuanForm" action="{{ route('staf.pengajuan.store') }}" method="POST">
                            @csrf
                            
                            <!-- Informasi Pengajuan -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pengajuan</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal Pengajuan</label>
                                        <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ date('Y-m-d') }}" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nomor Pengajuan</label>
                                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="AUTO-GENERATED" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Daftar Barang -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Barang yang Diajukan</h3>
                                
                                <!-- Item Barang -->
                                <div id="barangItems" class="space-y-4">
                                    <div class="barang-item border rounded-lg p-4 bg-gray-50">
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Pilih Barang</label>
                                                <select name="barang_id[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm barang-select" required>
                                                    <option value="">Pilih barang...</option>
                                                    @foreach($barangs as $barang)
                                                        <option value="{{ $barang->id }}" data-harga="{{ $barang->harga_saat_ini }}" data-satuan="{{ $barang->satuan }}">
                                                            {{ $barang->nama_barang }} (Rp {{ number_format($barang->harga_saat_ini, 0, ',', '.') }}/{{ $barang->satuan }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                                                <input type="number" name="jumlah[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm jumlah-input" min="1" required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Satuan</label>
                                                <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm satuan-display" readonly>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                                                <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm subtotal-display" readonly>
                                            </div>
                                        </div>
                                        <div class="mt-2 flex justify-end">
                                            <button type="button" class="text-red-600 hover:text-red-900 remove-item">Hapus</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Tambah Barang -->
                                <div class="mt-4">
                                    <button type="button" id="tambahBarang" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                                        + Tambah Barang Lain
                                    </button>
                                </div>
                            </div>

                            <!-- Total dan Catatan -->
                            <div class="mb-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                                        <textarea name="catatan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Ringkasan</h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span>Total Item:</span>
                                                <span id="totalItem">1</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Total Harga:</span>
                                                <span id="totalHarga" class="font-semibold">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="flex justify-end space-x-4">
                                <a href="{{ route('staf.dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                                    Batal
                                </a>
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                                    Ajukan Permintaan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Template untuk item barang baru
        const barangItemTemplate = `
            <div class="barang-item border rounded-lg p-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pilih Barang</label>
                        <select name="barang_id[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm barang-select" required>
                            <option value="">Pilih barang...</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->id }}" data-harga="{{ $barang->harga_saat_ini }}" data-satuan="{{ $barang->satuan }}">
                                    {{ $barang->nama_barang }} (Rp {{ number_format($barang->harga_saat_ini, 0, ',', '.') }}/{{ $barang->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                        <input type="number" name="jumlah[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm jumlah-input" min="1" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Satuan</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm satuan-display" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm subtotal-display" readonly>
                    </div>
                </div>
                <div class="mt-2 flex justify-end">
                    <button type="button" class="text-red-600 hover:text-red-900 remove-item">Hapus</button>
                </div>
            </div>
        `;

        // Tambah barang baru
        document.getElementById('tambahBarang').addEventListener('click', function() {
            const container = document.getElementById('barangItems');
            const newItem = document.createElement('div');
            newItem.innerHTML = barangItemTemplate;
            container.appendChild(newItem);
            updateSummary();
        });

        // Hapus item barang
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item')) {
                if (document.querySelectorAll('.barang-item').length > 1) {
                    e.target.closest('.barang-item').remove();
                    updateSummary();
                }
            }
        });

        // Update subtotal saat barang atau jumlah berubah
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('barang-select') || e.target.classList.contains('jumlah-input')) {
                const item = e.target.closest('.barang-item');
                updateItemSubtotal(item);
                updateSummary();
            }
        });

        // Update subtotal per item
        function updateItemSubtotal(item) {
            const select = item.querySelector('.barang-select');
            const jumlahInput = item.querySelector('.jumlah-input');
            const satuanDisplay = item.querySelector('.satuan-display');
            const subtotalDisplay = item.querySelector('.subtotal-display');

            if (select.value && jumlahInput.value) {
                const harga = parseFloat(select.selectedOptions[0].dataset.harga);
                const jumlah = parseFloat(jumlahInput.value);
                const satuan = select.selectedOptions[0].dataset.satuan;
                const subtotal = harga * jumlah;

                satuanDisplay.value = satuan;
                subtotalDisplay.value = 'Rp ' + subtotal.toLocaleString('id-ID');
            } else {
                satuanDisplay.value = '';
                subtotalDisplay.value = '';
            }
        }

        // Update ringkasan total
        function updateSummary() {
            const items = document.querySelectorAll('.barang-item');
            let totalItem = 0;
            let totalHarga = 0;

            items.forEach(item => {
                const subtotalDisplay = item.querySelector('.subtotal-display');
                if (subtotalDisplay.value) {
                    totalItem++;
                    totalHarga += parseFloat(subtotalDisplay.value.replace('Rp ', '').replace(/\./g, ''));
                }
            });

            document.getElementById('totalItem').textContent = totalItem;
            document.getElementById('totalHarga').textContent = 'Rp ' + totalHarga.toLocaleString('id-ID');
        }

        // Inisialisasi pertama kali
        document.addEventListener('DOMContentLoaded', function() {
            updateSummary();
        });
    </script>
</body>
</html>
