<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan History Gudang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #333;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .header h2 {
            font-size: 18px;
            font-weight: normal;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .header .info {
            font-size: 11px;
            color: #95a5a6;
        }

        .statistics {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .stat-item {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            background: #f8f9fa;
        }

        .stat-item .label {
            font-size: 11px;
            color: #7f8c8d;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-item .value {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }

        .stat-item.success .value {
            color: #27ae60;
        }

        .stat-item.danger .value {
            color: #e74c3c;
        }

        .stat-item.info .value {
            color: #3498db;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table thead {
            background: #34495e;
            color: white;
        }

        table thead th {
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table tbody td {
            padding: 10px 8px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 11px;
        }

        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        table tbody tr:hover {
            background: #e8f4f8;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .text-success {
            color: #27ae60;
            font-weight: 600;
        }

        .text-danger {
            color: #e74c3c;
            font-weight: 600;
        }

        .text-muted {
            color: #95a5a6;
            font-size: 10px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ecf0f1;
            text-align: center;
            font-size: 10px;
            color: #95a5a6;
        }

        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
        }

        .signature-box .title {
            font-size: 11px;
            margin-bottom: 60px;
        }

        .signature-box .name {
            font-weight: bold;
            border-top: 1px solid #333;
            padding-top: 5px;
            display: inline-block;
            min-width: 150px;
        }

        @media print {
            body {
                padding: 0;
            }

            .statistics {
                page-break-inside: avoid;
            }

            table {
                page-break-inside: auto;
            }

            table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            .signature-section {
                page-break-before: avoid;
            }
        }

        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN HISTORY GUDANG</h1>
        <h2>Rumah Sakit [Nama RS Anda]</h2>
        <div class="info">
            Dicetak pada: {{ now()->format('d F Y H:i:s') }}
            @if(request('tanggal_dari') || request('tanggal_sampai'))
                <br>
                Periode: 
                {{ request('tanggal_dari') ? \Carbon\Carbon::parse(request('tanggal_dari'))->format('d F Y') : '...' }}
                s/d
                {{ request('tanggal_sampai') ? \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d F Y') : '...' }}
            @endif
        </div>
    </div>

    {{-- Statistics --}}
    <div class="statistics">
        <div class="stat-item">
            <div class="label">Total Transaksi</div>
            <div class="value">{{ number_format($statistics['total_transaksi']) }}</div>
        </div>
        <div class="stat-item success">
            <div class="label">Total Penerimaan</div>
            <div class="value">{{ number_format($statistics['total_penerimaan']) }}</div>
        </div>
        <div class="stat-item danger">
            <div class="label">Total Pengiriman</div>
            <div class="value">{{ number_format($statistics['total_pengiriman']) }}</div>
        </div>
        <div class="stat-item info">
            <div class="label">Selisih</div>
            <div class="value">{{ number_format($statistics['selisih']) }}</div>
        </div>
    </div>

    {{-- Data Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 100px;">Waktu Proses</th>
                <th>Gudang</th>
                <th>Nama Barang</th>
                <th style="width: 80px;">Jenis</th>
                <th>Supplier</th>
                <th style="width: 80px;" class="text-center">Status</th>
                <th style="width: 70px;" class="text-end">Jumlah</th>
                <th style="width: 100px;">No Ref</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historyGudang as $index => $history)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <div>{{ $history->waktu_proses->format('d/m/Y') }}</div>
                    <div class="text-muted">{{ $history->waktu_proses->format('H:i') }}</div>
                </td>
                <td>
                    <div><strong>{{ $history->detailGudang->gudang->nama_gudang ?? '-' }}</strong></div>
                    <div class="text-muted">{{ $history->detailGudang->gudang->kode_gudang ?? '-' }}</div>
                </td>
                <td>
                    <div>{{ $history->detailGudang->nama_barang ?? '-' }}</div>
                    @if($history->detailGudang && $history->detailGudang->no_batch)
                        <div class="text-muted">Batch: {{ $history->detailGudang->no_batch }}</div>
                    @endif
                </td>
                <td>
                    <span class="badge badge-info">
                        {{ $history->detailGudang->jenis_barang ?? '-' }}
                    </span>
                </td>
                <td>
                    @if($history->supplier)
                        <div>{{ $history->supplier->nama_supplier }}</div>
                        <div class="text-muted">{{ $history->supplier->no_telp ?? '-' }}</div>
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    @if($history->status == 'penerimaan')
                        <span class="badge badge-success">Masuk</span>
                    @else
                        <span class="badge badge-danger">Keluar</span>
                    @endif
                </td>
                <td class="text-end">
                    <span class="{{ $history->status == 'penerimaan' ? 'text-success' : 'text-danger' }}">
                        {{ $history->status == 'penerimaan' ? '+' : '-' }}{{ number_format($history->jumlah) }}
                    </span>
                </td>
                <td>
                    @if($history->no_referensi)
                        <div>{{ $history->no_referensi }}</div>
                        <div class="text-muted">{{ $history->referensi_type ?? '-' }}</div>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 30px;">
                    Tidak ada data history gudang
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Signature Section --}}
    <div class="signature-section">
        <div class="signature-box">
            <div class="title">Dibuat Oleh,</div>
            <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
            <div class="text-muted">Staff Gudang</div>
        </div>
        <div class="signature-box">
            <div class="title">Diperiksa Oleh,</div>
            <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
            <div class="text-muted">Kepala Gudang</div>
        </div>
        <div class="signature-box">
            <div class="title">Disetujui Oleh,</div>
            <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
            <div class="text-muted">Direktur</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari sistem. Untuk informasi lebih lanjut hubungi bagian Gudang.</p>
        <p>&copy; {{ date('Y') }} Rumah Sakit [Nama RS Anda]. All rights reserved.</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>