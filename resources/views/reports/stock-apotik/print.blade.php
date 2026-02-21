<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stock Apotik - Print</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #000;
        }

        .header p {
            font-size: 11px;
            color: #666;
        }

        .statistics {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .stat-card h4 {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .stat-card .value {
            font-size: 16px;
            font-weight: bold;
            color: #000;
        }

        .filters-applied {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .filters-applied h4 {
            font-size: 11px;
            margin-bottom: 5px;
            color: #856404;
        }

        .filters-applied p {
            font-size: 10px;
            margin: 2px 0;
            color: #856404;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        table thead {
            background: #333;
            color: white;
        }

        table th {
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }

        table td {
            padding: 6px 5px;
            border-bottom: 1px solid #ddd;
        }

        table tbody tr:hover {
            background: #f8f9fa;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 9px;
            border-radius: 3px;
            font-weight: bold;
        }

        .badge-success { background: #198754; color: white; }
        .badge-warning { background: #ffc107; color: #000; }
        .badge-danger { background: #dc3545; color: white; }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        @media print {
            body {
                padding: 10px;
            }

            @page {
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN STOCK APOTIK</h1>
        <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
    </div>

    {{-- Active Filters --}}
    @if(!empty(array_filter($filters)))
    <div class="filters-applied">
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
    </div>
    @endif

    {{-- Statistics --}}
    <div class="statistics">
        <div class="stat-card">
            <h4>Total Items</h4>
            <div class="value">{{ number_format($statistics['total_items']) }}</div>
        </div>
        <div class="stat-card">
            <h4>Total Stock</h4>
            <div class="value">{{ number_format($statistics['total_stock']) }}</div>
        </div>
        <div class="stat-card">
            <h4>Stock Aman</h4>
            <div class="value">{{ number_format($statistics['stock_aman']) }}</div>
        </div>
        <div class="stat-card">
            <h4>Kadaluarsa</h4>
            <div class="value">{{ number_format($statistics['kadaluarsa']) }}</div>
        </div>
    </div>

    {{-- Data Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Kode Transaksi</th>
                <th>Tanggal</th>
                <th>Gudang</th>
                <th>Nama Barang</th>
                <th>Batch</th>
                <th class="text-center">Stock</th>
                <th class="text-center">Min</th>
                <th class="text-center">Retur</th>
                <th>Kadaluarsa</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockApotiks as $index => $stock)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $stock->stockApotik->kode_transaksi ?? 'N/A' }}</strong>
                </td>
                <td>{{ \Carbon\Carbon::parse($stock->stockApotik->tanggal_penerimaan ?? now())->format('d/m/Y') }}</td>
                <td>{{ $stock->stockApotik->gudang->nama_gudang ?? 'N/A' }}</td>
                <td>
                    {{ $stock->detailObatRs->obatRs->nama_obat ?? $stock->nama_barang ?? 'N/A' }}
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
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    @php
                        $badgeClass = 'badge-success';
                        $status = 'Aman';
                        if ($stock->stock_apotik <= 0) {
                            $badgeClass = 'badge-danger';
                            $status = 'Habis';
                        } elseif ($stock->stock_apotik <= $stock->min_persediaan) {
                            $badgeClass = 'badge-warning';
                            $status = 'Menipis';
                        }
                        
                        if ($stock->tanggal_kadaluarsa) {
                            $expDate = \Carbon\Carbon::parse($stock->tanggal_kadaluarsa);
                            if ($expDate->isPast()) {
                                $status .= ' / Exp';
                            }
                        }
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #f8f9fa; font-weight: bold;">
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
        <p>Total {{ $stockApotiks->count() }} item stock apotik</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>