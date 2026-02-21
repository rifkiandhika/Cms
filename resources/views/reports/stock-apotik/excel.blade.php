<table>
    {{-- Title --}}
    <tr>
        <td colspan="12" style="text-align: center; font-weight: bold; font-size: 16px;">
            LAPORAN STOCK APOTIK
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
        <td colspan="2">Total Items:</td>
        <td>{{ number_format($statistics['total_items']) }}</td>
        <td colspan="2">Total Stock:</td>
        <td colspan="2">{{ number_format($statistics['total_stock']) }}</td>
        <td>Total Retur:</td>
        <td>{{ number_format($statistics['total_retur']) }}</td>
        <td>Total Gudang:</td>
        <td colspan="2">{{ number_format($statistics['total_gudang']) }}</td>
    </tr>
    <tr>
        <td colspan="2">Stock Aman:</td>
        <td>{{ number_format($statistics['stock_aman']) }}</td>
        <td colspan="2">Stock Menipis:</td>
        <td colspan="2">{{ number_format($statistics['stock_menipis']) }}</td>
        <td>Stock Habis:</td>
        <td>{{ number_format($statistics['stock_habis']) }}</td>
        <td>Kadaluarsa:</td>
        <td colspan="2">{{ number_format($statistics['kadaluarsa']) }}</td>
    </tr>
    <tr>
        <td colspan="2">Akan Kadaluarsa:</td>
        <td>{{ number_format($statistics['akan_kadaluarsa']) }}</td>
        <td colspan="9"></td>
    </tr>
    <tr><td colspan="12"></td></tr>

    {{-- Filters Applied --}}
    @if(!empty(array_filter($filters)))
    <tr>
        <td colspan="12" style="font-weight: bold; background-color: #fff3cd;">FILTER YANG DITERAPKAN</td>
    </tr>
    @if(isset($filters['gudang_id']) && $filters['gudang_id'])
    <tr>
        <td colspan="2">Gudang:</td>
        <td colspan="10">ID: {{ $filters['gudang_id'] }}</td>
    </tr>
    @endif
    @if(isset($filters['nama_barang']) && $filters['nama_barang'])
    <tr>
        <td colspan="2">Nama Barang:</td>
        <td colspan="10">{{ $filters['nama_barang'] }}</td>
    </tr>
    @endif
    @if(isset($filters['no_batch']) && $filters['no_batch'])
    <tr>
        <td colspan="2">No Batch:</td>
        <td colspan="10">{{ $filters['no_batch'] }}</td>
    </tr>
    @endif
    @if(isset($filters['stock_status']) && $filters['stock_status'])
    <tr>
        <td colspan="2">Status Stock:</td>
        <td colspan="10">{{ ucfirst($filters['stock_status']) }}</td>
    </tr>
    @endif
    @if(isset($filters['kadaluarsa_status']) && $filters['kadaluarsa_status'])
    <tr>
        <td colspan="2">Status Kadaluarsa:</td>
        <td colspan="10">{{ ucfirst(str_replace('_', ' ', $filters['kadaluarsa_status'])) }}</td>
    </tr>
    @endif
    @if(isset($filters['tanggal_dari']) && $filters['tanggal_dari'])
    <tr>
        <td colspan="2">Tanggal Dari:</td>
        <td colspan="10">{{ date('d/m/Y', strtotime($filters['tanggal_dari'])) }}</td>
    </tr>
    @endif
    @if(isset($filters['tanggal_sampai']) && $filters['tanggal_sampai'])
    <tr>
        <td colspan="2">Tanggal Sampai:</td>
        <td colspan="10">{{ date('d/m/Y', strtotime($filters['tanggal_sampai'])) }}</td>
    </tr>
    @endif
    <tr><td colspan="12"></td></tr>
    @endif

    {{-- Table Header --}}
    <tr style="background-color: #495057; color: white; font-weight: bold;">
        <th>No</th>
        <th>Kode Transaksi</th>
        <th>Tanggal Terima</th>
        <th>Gudang</th>
        <th>Nama Barang</th>
        <th>Merk/Satuan</th>
        <th>No Batch</th>
        <th>Stock</th>
        <th>Min</th>
        <th>Retur</th>
        <th>Tanggal Kadaluarsa</th>
        <th>Status</th>
    </tr>

    {{-- Data Rows --}}
    @foreach($stockApotiks as $index => $stock)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $stock->stockApotik->kode_transaksi ?? 'N/A' }}</td>
        <td>{{ \Carbon\Carbon::parse($stock->stockApotik->tanggal_penerimaan ?? now())->format('d/m/Y') }}</td>
        <td>{{ $stock->stockApotik->gudang->nama_gudang ?? 'N/A' }}</td>
        <td>{{ $stock->detailObatRs->obatRs->nama_obat ?? $stock->nama_barang ?? 'N/A' }}</td>
        <td>
            @if($stock->detailSupplier)
                {{ $stock->detailSupplier->merk ?? '' }} {{ $stock->detailSupplier->satuan ?? '' }}
            @else
                -
            @endif
        </td>
        <td>{{ $stock->no_batch }}</td>
        <td>{{ $stock->stock_apotik }}</td>
        <td>{{ $stock->min_persediaan }}</td>
        <td>{{ $stock->retur }}</td>
        <td>
            @if($stock->tanggal_kadaluarsa)
                {{ \Carbon\Carbon::parse($stock->tanggal_kadaluarsa)->format('d/m/Y') }}
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
                        $status .= ' / Expired';
                    } elseif ($expDate->diffInDays(\Carbon\Carbon::today()) <= 90) {
                        $status .= ' / Akan Expired';
                    }
                }
            @endphp
            {{ $status }}
        </td>
    </tr>
    @endforeach

    {{-- Footer Total --}}
    <tr style="background-color: #f8f9fa; font-weight: bold;">
        <td colspan="7" style="text-align: right;">TOTAL:</td>
        <td>{{ $stockApotiks->sum('stock_apotik') }}</td>
        <td>-</td>
        <td>{{ $stockApotiks->sum('retur') }}</td>
        <td colspan="2"></td>
    </tr>

    {{-- Footer Info --}}
    <tr><td colspan="12"></td></tr>
    <tr>
        <td colspan="12" style="text-align: center; font-size: 10px; font-style: italic;">
            Total {{ $stockApotiks->count() }} item stock apotik
        </td>
    </tr>
</table>