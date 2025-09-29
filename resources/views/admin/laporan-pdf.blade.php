<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan {{ ucfirst($type) }} - Dapur MBG</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
        }
        .info {
            margin-bottom: 15px;
        }
        .info table {
            width: 100%;
            border-collapse: collapse;
        }
        .info td {
            padding: 3px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .detail-section {
            margin-top: 20px;
        }
        .detail-section h3 {
            margin-bottom: 10px;
            font-size: 14px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN {{ strtoupper($type) }}</h1>
        <p>DAPUR MBG</p>
        <p>Periode: {{ $startDate }} sampai {{ $endDate }}</p>
        <p>Tanggal Cetak: {{ $tanggalCetak }}</p>
    </div>

    @if($type == 'pengajuan')
        <div class="info">
            <table>
                <tr>
                    <td><strong>Total Pengajuan:</strong> {{ $pengajuans->count() }}</td>
                    <td><strong>Pending:</strong> {{ $pengajuans->where('status', 'pending')->count() }}</td>
                    <td><strong>Disetujui:</strong> {{ $pengajuans->where('status', 'approved')->count() }}</td>
                    <td><strong>Ditolak:</strong> {{ $pengajuans->where('status', 'rejected')->count() }}</td>
                </tr>
            </table>
        </div>

        @foreach($pengajuans as $index => $pengajuan)
            <div class="detail-section">
                <h3>{{ $index + 1 }}. Pengajuan: {{ $pengajuan->nomor_pengajuan }}</h3>
                <table class="table">
                    <tr>
                        <td width="20%"><strong>Nama Staf</strong></td>
                        <td width="30%">{{ $pengajuan->user->name }}</td>
                        <td width="20%"><strong>Tanggal</strong></td>
                        <td width="30%">{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>{{ ucfirst($pengajuan->status) }}</td>
                        <td><strong>Total Harga</strong></td>
                        <td>Rp {{ number_format($pengajuan->total_harga, 0, ',', '.') }}</td>
                    </tr>
                    @if($pengajuan->catatan)
                    <tr>
                        <td><strong>Catatan</strong></td>
                        <td colspan="3">{{ $pengajuan->catatan }}</td>
                    </tr>
                    @endif
                </table>

                <h4>Detail Barang:</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengajuan->items as $item)
                        <tr>
                            <td>{{ $item->barang->nama_barang }}</td>
                            <td>{{ $item->jumlah }} {{ $item->barang->satuan }}</td>
                            <td>Rp {{ number_format($item->harga_saat_ini, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if(($index + 1) % 3 == 0 && ($index + 1) < $pengajuans->count())
                <div class="page-break"></div>
            @endif
        @endforeach

    @else
        <div class="info">
            <table>
                <tr>
                    <td><strong>Total Invoice:</strong> {{ $invoices->count() }}</td>
                    <td><strong>Issued:</strong> {{ $invoices->where('status', 'issued')->count() }}</td>
                    <td><strong>Paid:</strong> {{ $invoices->where('status', 'paid')->count() }}</td>
                    <td><strong>Total Revenue:</strong> Rp {{ number_format($invoices->where('status', 'paid')->sum('total_harga'), 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

                @foreach($invoices as $index => $invoice)
                    <div class="detail-section">
                        <h3>{{ $index + 1 }}. Invoice: {{ $invoice->nomor_invoice }}</h3>
                        <table class="table">
                            <tr>
                                <td width="20%"><strong>Nomor Pengajuan</strong></td>
                                <td width="30%">{{ $invoice->pengajuan->nomor_pengajuan ?? 'N/A' }}</td>
                                <td width="20%"><strong>Nama Staf</strong></td>
                                <td width="30%">{{ $invoice->pengajuan->user->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Invoice</strong></td>
                                <td>{{ $invoice->tanggal_invoice->format('d/m/Y') }}</td>
                                <td><strong>Status</strong></td>
                                <td>{{ ucfirst($invoice->status) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Harga</strong></td>
                                <td>Rp {{ number_format($invoice->total_harga, 0, ',', '.') }}</td>
                                <td><strong>Dibuat oleh</strong></td>
                                <td>{{ $invoice->issuedBy->name ?? 'N/A' }}</td>
                            </tr>
                        </table>

                        @if($invoice->pengajuan && $invoice->pengajuan->items)
                        <h4>Detail Barang:</h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->pengajuan->items as $item)
                                <tr>
                                    <td>{{ $item->barang->nama_barang ?? 'N/A' }}</td>
                                    <td>{{ $item->jumlah }} {{ $item->barang->satuan ?? '' }}</td>
                                    <td>Rp {{ number_format($item->harga_saat_ini, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                    
                    @if(($index + 1) % 3 == 0 && ($index + 1) < $invoices->count())
                        <div class="page-break"></div>
                    @endif
                @endforeach
    @endif

    <div class="footer">
        <p>Dicetak oleh Sistem Informasi Pengajuan Barang Dapur MBG</p>
        <p>Tanggal Cetak: {{ $tanggalCetak }}</p>
    </div>
</body>
</html>
