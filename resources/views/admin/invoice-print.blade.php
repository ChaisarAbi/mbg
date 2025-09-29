<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->nomor_invoice }} - Dapur MBG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
        }
        .print-only {
            display: none;
        }
    </style>
</head>
<body class="bg-white">
    <div class="container mx-auto px-8 py-12">
        <!-- Print Controls -->
        <div class="no-print mb-6 flex justify-end space-x-4">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                🖨️ Cetak Invoice
            </button>
            <button onclick="window.close()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                ✕ Tutup
            </button>
        </div>

        <!-- Invoice Header -->
        <div class="border-2 border-gray-800 rounded-lg p-8">
            <!-- Company Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">DAPUR MBG</h1>
                <p class="text-gray-600">Sistem Informasi Pengajuan Barang</p>
                <p class="text-gray-500 text-sm">Jl. Contoh No. 123, Jakarta</p>
                <p class="text-gray-500 text-sm">Telp: (021) 123-4567 | Email: info@dapurbmg.com</p>
            </div>

            <!-- Invoice Title -->
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">INVOICE</h2>
                <p class="text-gray-600">Nomor: {{ $invoice->nomor_invoice }}</p>
            </div>

            <!-- Invoice Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Informasi Invoice</h3>
                    <div class="space-y-1">
                        <p><span class="font-medium">Nomor Invoice:</span> {{ $invoice->nomor_invoice }}</p>
                        <p><span class="font-medium">Tanggal Invoice:</span> {{ $invoice->tanggal_invoice->format('d F Y') }}</p>
                        <p><span class="font-medium">Status:</span> 
                            @if($invoice->status === 'draft') Draft
                            @elseif($invoice->status === 'issued') Diterbitkan
                            @else Lunas @endif
                        </p>
                        @if($invoice->issuedBy)
                        <p><span class="font-medium">Diterbitkan oleh:</span> {{ $invoice->issuedBy->name }}</p>
                        @endif
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Informasi Pengajuan</h3>
                    <div class="space-y-1">
                        <p><span class="font-medium">Nomor Pengajuan:</span> {{ $invoice->pengajuan->nomor_pengajuan }}</p>
                        <p><span class="font-medium">Diajukan oleh:</span> {{ $invoice->pengajuan->user->name }}</p>
                        <p><span class="font-medium">Tanggal Pengajuan:</span> {{ $invoice->pengajuan->tanggal_pengajuan->format('d F Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Barang</h3>
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold">No</th>
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold">Nama Barang</th>
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold">Jumlah</th>
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold">Harga Satuan</th>
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->pengajuan->items as $index => $item)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $item->barang->nama_barang }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $item->jumlah }} {{ $item->barang->satuan }}</td>
                            <td class="border border-gray-300 px-4 py-2">Rp {{ number_format($item->harga_saat_ini, 0, ',', '.') }}</td>
                            <td class="border border-gray-300 px-4 py-2">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="4" class="border border-gray-300 px-4 py-2 text-right font-semibold">Total</td>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Rp {{ number_format($invoice->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Notes -->
            @if($invoice->catatan)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Catatan</h3>
                <div class="border border-gray-300 rounded p-4">
                    <p class="text-gray-700">{{ $invoice->catatan }}</p>
                </div>
            </div>
            @endif

            <!-- Footer -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
                <div class="text-center">
                    <div class="border-t-2 border-gray-800 pt-4">
                        <p class="font-semibold">Penerima</p>
                        <div class="mt-16"></div>
                        <p class="border-t border-gray-300 pt-2">(___________________)</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="border-t-2 border-gray-800 pt-4">
                        <p class="font-semibold">Dapur MBG</p>
                        <div class="mt-16"></div>
                        <p class="border-t border-gray-300 pt-2">(___________________)</p>
                    </div>
                </div>
            </div>

            <!-- Print Date -->
            <div class="text-center mt-8 text-sm text-gray-500">
                <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
            </div>
        </div>

        <!-- Print Watermark -->
        <div class="print-only fixed bottom-0 right-0 p-4 text-gray-300 text-sm">
            Invoice {{ $invoice->nomor_invoice }} - Dapur MBG
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() {
        //     window.print();
        // }
    </script>
</body>
</html>
