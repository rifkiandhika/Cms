{{-- resources/views/reports/purchase-orders/excel.blade.php --}}
<table>
    {{-- Title --}}
    <tr>
        <td colspan="12" style="text-align: center; font-weight: bold; font-size: 16px;">
            LAPORAN PURCHASE ORDER
        </td>
    </tr>
    <tr>
        <td colspan="12" style="text-align: center; font-size: 10px;">
            Dicetak pada: {{ date('d F Y H:i') }}
        </td>
    </tr>
    <tr><td colspan="12"></td></tr>

    {{-- Statistics --}}
    <tr>
        <td colspan="12" style="font-weight: bold; background-color: #e9ecef;">STATISTIK</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Total PO:</strong></td>
        <td>{{ number_format($statistics['total_po']) }}</td>
        <td colspan="2"><strong>Total Nilai:</strong></td>
        <td colspan="2">Rp {{ number_format($statistics['total_nilai'], 0, ',', '.') }}</td>
        <td><strong>Diterima:</strong></td>
        <td>{{ number_format($statistics['diterima']) }}</td>
        <td><strong>Outstanding:</strong></td>
        <td colspan="2">Rp {{ number_format($statistics['total_outstanding'], 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Internal:</strong></td>
        <td>{{ number_format($statistics['internal']) }}</td>
        <td colspan="2"><strong>Eksternal:</strong></td>
        <td colspan="2">{{ number_format($statistics['eksternal']) }}</td>
        <td><strong>Draft:</strong></td>
        <td>{{ number_format($statistics['draft'] ?? 0) }}</td>
        <td><strong>Ditolak:</strong></td>
        <td colspan="2">{{ number_format($statistics['ditolak']) }}</td>
    </tr>
    <tr><td colspan="12"></td></tr>

    {{-- Filters Applied --}}
    @if(!empty(array_filter($filters)))
    <tr>
        <td colspan="12" style="font-weight: bold; background-color: #fff3cd;">FILTER YANG DITERAPKAN</td>
    </tr>
    @if(isset($filters['tanggal_dari']) && $filters['tanggal_dari'])
    <tr>
        <td colspan="2"><strong>Tanggal Dari:</strong></td>
        <td colspan="10">{{ date('d/m/Y', strtotime($filters['tanggal_dari'])) }}</td>
    </tr>
    @endif
    @if(isset($filters['tanggal_sampai']) && $filters['tanggal_sampai'])
    <tr>
        <td colspan="2"><strong>Tanggal Sampai:</strong></td>
        <td colspan="10">{{ date('d/m/Y', strtotime($filters['tanggal_sampai'])) }}</td>
    </tr>
    @endif
    @if(isset($filters['status']) && $filters['status'])
    <tr>
        <td colspan="2"><strong>Status:</strong></td>
        <td colspan="10">{{ str_replace('_', ' ', ucwords($filters['status'], '_')) }}</td>
    </tr>
    @endif
    @if(isset($filters['tipe_po']) && $filters['tipe_po'])
    <tr>
        <td colspan="2"><strong>Tipe PO:</strong></td>
        <td colspan="10">{{ ucfirst($filters['tipe_po']) }}</td>
    </tr>
    @endif
    @if(isset($filters['no_po']) && $filters['no_po'])
    <tr>
        <td colspan="2"><strong>No PO:</strong></td>
        <td colspan="10">{{ $filters['no_po'] }}</td>
    </tr>
    @endif
    @if(isset($filters['id_supplier']) && $filters['id_supplier'])
    <tr>
        <td colspan="2"><strong>Supplier:</strong></td>
        <td colspan="10">{{ $filters['supplier_name'] ?? 'ID: ' . $filters['id_supplier'] }}</td>
    </tr>
    @endif
    <tr><td colspan="12"></td></tr>
    @endif

    {{-- Table Header --}}
    <tr style="background-color: #495057; color: white; font-weight: bold;">
        <th style="text-align: center;">No</th>
        <th>No PO</th>
        <th>Tanggal</th>
        <th style="text-align: center;">Tipe</th>
        <th>Unit Pemohon</th>
        <th>Pemohon</th>
        <th>Unit Tujuan</th>
        <th>Supplier/Tujuan</th>
        <th>Nama Barang</th>
        <th style="text-align: center;">Status</th>
        <th style="text-align: right;">Grand Total</th>
        <th style="text-align: right;">Total Diterima</th>
    </tr>

    {{-- Data Rows --}}
    @foreach($purchaseOrders as $index => $po)
    <tr>
        <td style="text-align: center;">{{ $index + 1 }}</td>
        <td>{{ $po->no_po }}</td>
        <td>{{ \Carbon\Carbon::parse($po->tanggal_permintaan)->format('d/m/Y') }}</td>
        <td style="text-align: center;">{{ ucfirst($po->tipe_po) }}</td>
        <td>{{ ucfirst($po->unit_pemohon) }}</td>
        <td>{{ $po->karyawanPemohon->nama_lengkap ?? 'N/A' }}</td>
        <td>{{ ucfirst($po->unit_tujuan ?? 'N/A') }}</td>
        <td>{{ $po->supplier->nama_supplier ?? ucfirst($po->unit_tujuan ?? 'N/A') }}</td>
        <td>
            @if($po->items->count() > 0)
                @foreach($po->items as $item)
                    {{ $loop->iteration }}. {{ $item->produk->nama ?? 'N/A' }} (Qty: {{ number_format($item->qty_diminta) }})
                    @if(!$loop->last){{ chr(10) }}@endif
                @endforeach
            @else
                -
            @endif
        </td>
        <td style="text-align: center;">{{ str_replace('_', ' ', ucwords($po->status, '_')) }}</td>
        <td style="text-align: right;">{{ $po->grand_total }}</td>
        <td style="text-align: right;">{{ $po->grand_total_diterima ?? 0 }}</td>
    </tr>
    @endforeach

    {{-- Footer Total --}}
    <tr style="background-color: #f8f9fa; font-weight: bold;">
        <td colspan="10" style="text-align: right;"><strong>TOTAL:</strong></td>
        <td style="text-align: right;">{{ $purchaseOrders->sum('grand_total') }}</td>
        <td style="text-align: right;">{{ $purchaseOrders->sum('grand_total_diterima') }}</td>
    </tr>

    {{-- Footer Info --}}
    <tr><td colspan="12"></td></tr>
    <tr>
        <td colspan="12" style="text-align: center; font-size: 10px; font-style: italic;">
            Total {{ $purchaseOrders->count() }} purchase order | Dicetak dari Sistem Manajemen Purchase Order
        </td>
    </tr>
</table>