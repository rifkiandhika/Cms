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
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 3px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #666;
            margin-bottom: 5px;
        }

        .header .info {
            font-size: 9px;
            color: #999;
        }

        .statistics {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .statistics td {
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            background: #f8f9fa;
        }

        .statistics .label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            display: block;
            margin-bottom: 3px;
        }

        .statistics .value {
            font-size: 16px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead {
            background: #34495e;
            color: white;
        }

        table thead th {
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }

        table tbody td {
            padding: 6px 5px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 9px;
        }

        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 600;
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
            font-size: 8px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ecf0f1;
            text-align: center;
            font-size: 8px;
            color: #95a5a6;
        }

        .signature-section {
            margin-top: 30px;
            width: 100%;
        }

        .signature-box {
            display: inline-block;
            width: 32%;
            text-align: center;
            vertical-align: top;
        }

        .signature-box .title {
            font-size: 9px;
            margin-bottom: 40px;
        }

        .signature-box .name {
            font-weight: bold;
            border-top: 1px solid #333;
            padding-top: 3px;
            display: inline-block;
            min-width: 100px;
            font-size: 9px;
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
    <table class="statistics">
        <tr>
            <td>
                <span class="label">Total Transaksi</span>
                <span class="value">{{ number_format($statistics['total_transaksi']) }}</span>
            </td>
            <td>
                <span class="label">Total Penerimaan</span>
                <span class="value text-success">{{ number_format($statistics['total_penerimaan']) }}</span>
            </td>
            <td>
                <span class="label">Total Pengiriman</span>
                <span class="value text-danger">{{ number_format($statistics['total_pengiriman']) }}</span>
            </td>
            <td>
                <span class="label">Selisih</span>
                <span class="value">{{ number_format($statistics['selisih']) }}</span>
            </td>
        </tr>
    </table>

    {{-- Data Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 20px;">No</th>
                <th style="width: 60px;">Waktu</th>
                <th>Gudang</th>
                <th>Nama Barang</th>
                <th style="width: 50px;">Jenis</th>
                <th>Supplier</th>
                <th style="width: 45px;" class="text-center">Status</th>
                <th style="width: 50px;" class="text-end">Jumlah</th>
                <th style="width: 60px;">No Ref</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historyGudang as $index => $history)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    {{ $history->waktu_proses->format('d/m/Y') }}<br>
                    <span class="text-muted">{{ $history->waktu_proses->format('H:i') }}</span>
                </td>
                <td>
                    <strong>{{ $history->detailGudang->gudang->nama_gudang ?? '-' }}</strong><br>
                    <span class="text-muted">{{ $history->detailGudang->gudang->kode_gudang ?? '-' }}</span>
                </td>
                <td>
                    {{ $history->detailGudang->nama_barang ?? '-' }}
                    @if($history->detailGudang && $history->detailGudang->no_batch)
                        <br><span class="text-muted">Batch: {{ $history->detailGudang->no_batch }}</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-info">
                        {{ $history->detailGudang->jenis_barang ?? '-' }}
                    </span>
                </td>
                <td>
                    @if($history->supplier)
                        {{ $history->supplier->nama_supplier }}<br>
                        <span class="text-muted">{{ $history->supplier->no_telp ?? '-' }}</span>
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
                        {{ $history->no_referensi }}<br>
                        <span class="text-muted">{{ $history->referensi_type ?? '-' }}</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px;">
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
            <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
            <div class="text-muted">Staff Gudang</div>
        </div>
        <div class="signature-box">
            <div class="title">Diperiksa Oleh,</div>
            <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
            <div class="text-muted">Kepala Gudang</div>
        </div>
        <div class="signature-box">
            <div class="title">Disetujui Oleh,</div>
            <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
            <div class="text-muted">Direktur</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari sistem.</p>
        <p>&copy; {{ date('Y') }} Rumah Sakit [Nama RS Anda]. All rights reserved.</p>
    </div>
</body>
</html>