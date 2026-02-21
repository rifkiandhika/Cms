{{-- resources/views/reports/purchase-orders/print.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Purchase Order - Print</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #333;
            padding-bottom: 12px;
        }

        .header h1 {
            font-size: 22px;
            margin-bottom: 5px;
            color: #000;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        .statistics {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .stat-card {
            border: 1px solid #ddd;
            padding: 8px;
            border-radius: 4px;
            text-align: center;
        }

        .stat-card h4 {
            font-size: 9px;
            color: #666;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .stat-card .value {
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }

        .filters-applied {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 8px;
            margin-bottom: 12px;
            border-radius: 4px;
        }

        .filters-applied h4 {
            font-size: 10px;
            margin-bottom: 4px;
            color: #856404;
        }

        .filters-applied p {
            font-size: 9px;
            margin: 2px 0;
            color: #856404;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 10px;
        }

        table thead {
            background: #333;
            color: white;
        }

        table th {
            padding: 7px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            border: 1px solid #000;
        }

        table td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        table tbody tr:hover {
            background: #e9ecef;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8px;
            border-radius: 3px;
            font-weight: bold;
        }

        .badge-primary { background: #0d6efd; color: white; }
        .badge-info { background: #0dcaf0; color: #000; }
        .badge-success { background: #198754; color: white; }
        .badge-warning { background: #ffc107; color: #000; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-secondary { background: #6c757d; color: white; }

        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }

        .product-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .product-list li {
            margin-bottom: 3px;
            font-size: 9px;
        }

        .product-list li strong {
            color: #000;
        }

        .product-list li small {
            color: #666;
        }

        tfoot tr {
            background: #f8f9fa;
            font-weight: bold;
        }

        tfoot td {
            border: 2px solid #000;
            padding: 7px 4px;
        }

        @media print {
            body {
                padding: 8px;
            }

            .page-break {
                page-break-after: always;
            }

            @page {
                margin: 12mm;
                size: A4 landscape;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN PURCHASE ORDER</h1>
        <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
    </div>

    {{-- Active Filters --}}
    @if(!empty(array_filter($filters)))
    <div class="filters-applied">
        <h4>Filter yang Diterapkan:</h4>
        @if(isset($filters['tanggal_dari']) && $filters['tanggal_dari'])
            <p><strong>Tanggal Dari:</strong> {{ date('d/m/Y', strtotime($filters['tanggal_dari'])) }}</p>
        @endif
        @if(isset($filters['tanggal_sampai']) && $filters['tanggal_sampai'])
            <p><strong>Tanggal Sampai:</strong> {{ date('d/m/Y', strtotime($filters['tanggal_sampai'])) }}</p>
        @endif
        @if(isset($filters['status']) && $filters['status'])
            <p><strong>Status:</strong> {{ str_replace('_', ' ', ucwords($filters['status'], '_')) }}</p>
        @endif
        @if(isset($filters['tipe_po']) && $filters['tipe_po'])
            <p><strong>Tipe PO:</strong> {{ ucfirst($filters['tipe_po']) }}</p>
        @endif
        @if(isset($filters['no_po']) && $filters['no_po'])
            <p><strong>No PO:</strong> {{ $filters['no_po'] }}</p>
        @endif
    </div>
    @endif

    {{-- Statistics --}}
    <div class="statistics">
        <div class="stat-card">
            <h4>Total PO</h4>
            <div class="value">{{ number_format($statistics['total_po']) }}</div>
        </div>
        <div class="stat-card">
            <h4>Total Nilai</h4>
            <div class="value">Rp {{ number_format($statistics['total_nilai'] / 1000000, 1) }}jt</div>
        </div>
        <div class="stat-card">
            <h4>Diterima</h4>
            <div class="value">{{ number_format($statistics['diterima']) }}</div>
        </div>
        <div class="stat-card">
            <h4>Outstanding</h4>
            <div class="value">Rp {{ number_format($statistics['total_outstanding'] / 1000000, 1) }}jt</div>
        </div>
    </div>

    {{-- Data Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 80px;">No PO</th>
                <th style="width: 60px;">Tanggal</th>
                <th class="text-center" style="width: 50px;">Tipe</th>
                <th style="width: 90px;">Pemohon</th>
                <th style="width: 100px;">Supplier/Tujuan</th>
                <th style="width: 150px;">Nama Barang</th>
                <th class="text-center" style="width: 80px;">Status</th>
                <th class="text-right" style="width: 80px;">Grand Total</th>
                <th class="text-right" style="width: 80px;">Diterima</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrders as $index => $po)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $po->no_po }}</strong><br>
                    <small>{{ $po->unit_pemohon }} → {{ $po->unit_tujuan ?? 'N/A' }}</small>
                </td>
                <td>{{ \Carbon\Carbon::parse($po->tanggal_permintaan)->format('d/m/Y') }}</td>
                <td class="text-center">
                    @if($po->tipe_po == 'internal')
                        <span class="badge badge-primary">INT</span>
                    @else
                        <span class="badge badge-info">EXT</span>
                    @endif
                </td>
                <td>
                    {{ $po->karyawanPemohon->nama_lengkap ?? 'N/A' }}<br>
                    <small>{{ ucfirst($po->unit_pemohon) }}</small>
                </td>
                <td>
                    @if($po->supplier)
                        <strong>{{ $po->supplier->nama_supplier }}</strong>
                    @else
                        {{ ucfirst($po->unit_tujuan ?? 'N/A') }}
                    @endif
                </td>
                <td>
                    @if($po->items->count() > 0)
                        <ul class="product-list">
                            @foreach($po->items as $item)
                            <li>
                                <strong>{{ $loop->iteration }}.</strong> 
                                {{ $item->produk->nama ?? 'N/A' }}
                                <small>(Qty: {{ number_format($item->qty_diminta) }})</small>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <span>-</span>
                    @endif
                </td>
                <td class="text-center">
                    @php
                        $statusConfig = [
                            'draft' => 'secondary',
                            'menunggu_persetujuan_kepala_gudang' => 'warning',
                            'menunggu_persetujuan_kasir' => 'warning',
                            'disetujui' => 'success',
                            'dikirim_ke_supplier' => 'info',
                            'dalam_pengiriman' => 'info',
                            'diterima' => 'success',
                            'ditolak' => 'danger',
                            'dibatalkan' => 'secondary'
                        ];
                    @endphp
                    <span class="badge badge-{{ $statusConfig[$po->status] ?? 'secondary' }}">
                        {{ str_replace('_', ' ', ucwords($po->status, '_')) }}
                    </span>
                </td>
                <td class="text-right">
                    <strong>Rp {{ number_format($po->grand_total, 0, ',', '.') }}</strong><br>
                    <small>{{ $po->items->count() }} item</small>
                </td>
                <td class="text-right">
                    @if($po->grand_total_diterima)
                        <strong>Rp {{ number_format($po->grand_total_diterima, 0, ',', '.') }}</strong><br>
                        <small>{{ number_format(($po->grand_total_diterima / $po->grand_total) * 100, 1) }}%</small>
                    @else
                        <span>-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($purchaseOrders->sum('grand_total'), 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($purchaseOrders->sum('grand_total_diterima'), 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari sistem</p>
        <p>Halaman ini merupakan rangkuman dari {{ $purchaseOrders->count() }} purchase order</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>