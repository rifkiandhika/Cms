<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Stock Apotik</title>
    <style>
        @page {
            margin: 20mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #000;
        }

        .header h1 {
            font-size: 18pt;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .header p {
            font-size: 8pt;
            color: #666;
        }

        .info-box {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .info-box h3 {
            font-size: 11pt;
            margin-bottom: 8px;
            color: #000;
            border-bottom: 1px solid #999;
            padding-bottom: 3px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .stat-row {
            display: table-row;
        }

        .stat-cell {
            display: table-cell;
            width: 25%;
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }

        .stat-cell .label {
            font-size: 7pt;
            color: #666;
            text-transform: uppercase;
            display: block;
            margin-bottom: 3px;
        }

        .stat-cell .value {
            font-size: 12pt;
            font-weight: bold;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8pt;
        }

        table thead {
            background: #333;
            color: white;
        }

        table th {
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 7pt;
            border: 1px solid #000;
        }

        table td {
            padding: 5px 4px;
            font-size: 7pt;
            border: 1px solid #ddd;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        table tfoot {
            background: #e9e9e9;
            font-weight: bold;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 6pt;
            border-radius: 2px;
            font-weight: bold;
        }

        .badge-success { background: #198754; color: white; }
        .badge-warning { background: #ffc107; color: #000; }
        .badge-danger { background: #dc3545; color: white; }

        .filters-box {
            background: #fff9e6;
            border: 1px solid #ffc107;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .filters-box h4 {
            font-size: 9pt;
            margin-bottom: 5px;
            color: #856404;
        }

        .filters-box p {
            font-size: 7pt;
            margin: 2px 0;
            color: #856404;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 7pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN STOCK APOTIK</h1>
        <p>Dicetak pada: {{ date('d F Y H:i') }} WIB</p>
    </div>

    {{-- Active Filters --}}
    @if(!empty(array_filter($filters)))
    <div class="filters-box">
        <h4>Filter yang Diterapkan:</h4>
        @if(isset($filters['nama_barang']) && $filters['nama_barang'])
            <p><strong>Nama Barang:</strong> {{ $filters['nama_barang'] }}</p>
        @endif
        @if(isset($filters['no_batch']) && $filters['no_batch'])
            <p><strong>No Batch:</strong> {{ $filters['no_batch'] }}</p>
        @endif
        @if(isset($filters['stock_status']) && $filters['stock_status'])
            <p><strong>Status Stock:</strong> {{ ucfirst($filters['stock_status']) }}</p>
        @endif
        @if(isset($filters['kadaluarsa_status']) && $filters['kadaluarsa_status'])
            <p><strong>Status Kadaluarsa:</strong> {{ ucfirst(str_replace('_', ' ', $filters['kadaluarsa_status'])) }}</p>
        @endif
        @if(isset($filters['tanggal_dari']) && $filters['tanggal_dari'])
            <p><strong>Tanggal Dari:</strong> {{ date('d/m/Y', strtotime($filters['tanggal_dari'])) }}</p>
        @endif
        @if(isset($filters['tanggal_sampai']) && $filters['tanggal_sampai'])
            <p><strong>Tanggal Sampai:</strong> {{ date('d/m/Y', strtotime($filters['tanggal_sampai'])) }}</p>
        @endif
    </div>
    @endif

    {{-- Statistics --}}
    <div class="stats-grid">
        <div class="stat-row">
            <div class="stat-cell">
                <span class="label">Total Items</span>
                <span class="value">{{ number_format($statistics['total_items']) }}</span>
            </div>
            <div class="stat-cell">
                <span class="label">Total Stock</span>
                <span class="value">{{ number_format($statistics['total_stock']) }}</span>
            </div>
            <div class="stat-cell">
                <span class="label">Stock Aman</span>
                <span class="value">{{ number_format($statistics['stock_aman']) }}</span>
            </div>
            <div class="stat-cell">
                <span class="label">Kadaluarsa</span>
                <span class="value">{{ number_format($statistics['kadaluarsa']) }}</span>
            </div>
        </div>
    </div>

    {{-- Summary Table --}}
    <div class="info-box">
        <h3>Ringkasan</h3>
        <table style="border: none; margin-bottom: 0;">
            <tr>
                <td style="border: none; width: 30%; font-weight: bold;">Stock Menipis:</td>
                <td style="border: none;">{{ number_format($statistics['stock_menipis']) }}</td>
                <td style="border: none; width: 30%; font-weight: bold;">Stock Habis:</td>
                <td style="border: none;">{{ number_format($statistics['stock_habis']) }}</td>
            </tr>
            <tr>
                <td style="border: none; font-weight: bold;">Total Retur:</td>
                <td style="border: none;">{{ number_format($statistics['total_retur']) }}</td>
                <td style="border: none; font-weight: bold;">Akan Kadaluarsa:</td>
                <td style="border: none;">{{ number_format($statistics['akan_kadaluarsa']) }}</td>
            </tr>
        </table>
    </div>

    {{-- Data Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 20px;">No</th>
                <th style="width: 70px;">Kode Transaksi</th>
                <th style="width: 50px;">Tgl Terima</th>
                <th style="width: 70px;">Gudang</th>
                <th style="width: 120px;">Nama Barang</th>
                <th style="width: 50px;">Batch</th>
                <th style="width: 40px;" class="text-center">Stock</th>
                <th style="width: 30px;" class="text-center">Min</th>
                <th style="width: 35px;" class="text-center">Retur</th>
                <th style="width: 50px;">Kadaluarsa</th>
                <th style="width: 55px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockApotiks as $index => $stock)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $stock->stockApotik->kode_transaksi ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($stock->stockApotik->tanggal_penerimaan ?? now())->format('d/m/Y') }}</td>
                <td>{{ $stock->stockApotik->gudang->nama_gudang ?? 'N/A' }}</td>
                <td>
                    <strong>{{ $stock->detailObatRs->obatRs->nama_obat ?? $stock->nama_barang ?? 'N/A' }}</strong>
                    @if($stock->detailSupplier && ($stock->detailSupplier->merk || $stock->detailSupplier->satuan))
                        <br><small>{{ $stock->detailSupplier->merk }} {{ $stock->detailSupplier->satuan }}</small>
                    @endif
                </td>
                <td>{{ $stock->no_batch }}</td>
                <td class="text-center">
                    <strong>{{ number_format($stock->stock_apotik) }}</strong>
                </td>
                <td class="text-center">{{ number_format($stock->min_persediaan) }}</td>
                <td class="text-center">{{ number_format($stock->retur) }}</td>
                <td>
                    @if($stock->tanggal_kadaluarsa)
                        {{ \Carbon\Carbon::parse($stock->tanggal_kadaluarsa)->format('d/m/Y') }}
                        @php
                            $diff = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($stock->tanggal_kadaluarsa), false);
                        @endphp
                        <br><small>({{ $diff > 0 ? $diff . ' hari' : 'Expired' }})</small>
                    @else
                        -
                    @endif
                </td>
                <td>
                    @php
                        $status = 'Aman';
                        if ($stock->stock_apotik <= 0) $status = 'Habis';
                        elseif ($stock->stock_apotik <= $stock->min_persediaan) $status = 'Menipis';
                        
                        if ($stock->tanggal_kadaluarsa) {
                            $expDate = \Carbon\Carbon::parse($stock->tanggal_kadaluarsa);
                            if ($expDate->isPast()) {
                                $status .= ' / Exp';
                            } elseif ($expDate->diffInDays(\Carbon\Carbon::today()) <= 90) {
                                $status .= ' / Akan Exp';
                            }
                        }
                    @endphp
                    <span class="badge 
                        @if($stock->stock_apotik <= 0) badge-danger
                        @elseif($stock->stock_apotik <= $stock->min_persediaan) badge-warning
                        @else badge-success
                        @endif
                    ">{{ $status }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right">TOTAL:</td>
                <td class="text-center">{{ number_format($stockApotiks->sum('stock_apotik')) }}</td>
                <td class="text-center">-</td>
                <td class="text-center">{{ number_format($stockApotiks->sum('retur')) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari sistem</p>
        <p>Total {{ $stockApotiks->count() }} item stock apotik dalam laporan ini</p>
    </div>
</body>
</html>