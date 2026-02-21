@extends('layouts.app')

@section('title', 'Laporan Gudang')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Laporan Gudang</li>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">
                    <i class="ri-building-2-line me-2"></i>Laporan History Gudang
                </h4>
                <div class="page-title-right">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="ri-filter-3-line me-1"></i>Filter
                    </button>
                    <div class="btn-group ms-2">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="ri-download-2-line me-1"></i>Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportData('excel')">
                                <i class="ri-file-excel-2-line me-2 text-success"></i>Excel
                            </a></li>
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportData('pdf')">
                                <i class="ri-file-pdf-line me-2 text-danger"></i>PDF
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="printReport()">
                                <i class="ri-printer-line me-2 text-primary"></i>Print
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        {{-- Total Transaksi --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-lg rounded-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <span class="avatar-title rounded-3">
                                    <i class="ri-file-list-3-line fs-2 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Transaksi</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($statistics['total_transaksi']) }}</h3>
                            <small class="text-muted">Transaksi</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <div class="row text-center g-0">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block">Penerimaan</small>
                            <span class="fw-semibold text-success">{{ number_format($statistics['jumlah_penerimaan']) }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Pengiriman</small>
                            <span class="fw-semibold text-danger">{{ number_format($statistics['jumlah_pengiriman']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Penerimaan --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-lg rounded-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <span class="avatar-title rounded-3">
                                    <i class="ri-arrow-down-circle-line fs-2 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Penerimaan</p>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($statistics['total_penerimaan']) }}</h3>
                            <small class="text-muted">Item Masuk</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Pengiriman --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-lg rounded-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <span class="avatar-title rounded-3">
                                    <i class="ri-arrow-up-circle-line fs-2 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Pengiriman</p>
                            <h3 class="mb-0 fw-bold text-danger">{{ number_format($statistics['total_pengiriman']) }}</h3>
                            <small class="text-muted">Item Keluar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Selisih --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-lg rounded-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <span class="avatar-title rounded-3">
                                    <i class="ri-bar-chart-box-line fs-2 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Selisih</p>
                            <h3 class="mb-0 fw-bold {{ $statistics['selisih'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($statistics['selisih']) }}
                            </h3>
                            <small class="text-muted">Masuk - Keluar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="collapse mb-4" id="filterCollapse">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0"><i class="ri-filter-3-line me-2"></i>Filter Laporan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.gudangs.index') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        {{-- Tanggal --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tanggal Dari</label>
                            <input type="date" class="form-control" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tanggal Sampai</label>
                            <input type="date" class="form-control" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
                        </div>

                        {{-- Gudang --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Gudang</label>
                            <select class="form-select" name="gudang_id">
                                <option value="">-- Semua Gudang --</option>
                                @foreach($gudangs as $gudang)
                                    <option value="{{ $gudang->id }}" {{ request('gudang_id') == $gudang->id ? 'selected' : '' }}>
                                        {{ $gudang->nama_gudang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Supplier --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Supplier</label>
                            <select class="form-select" name="supplier_id">
                                <option value="">-- Semua Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id_supplier }}" {{ request('supplier_id') == $supplier->id_supplier ? 'selected' : '' }}>
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status">
                                <option value="">-- Semua Status --</option>
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jenis Barang --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Jenis Barang</label>
                            <select class="form-select" name="barang_type">
                                <option value="">-- Semua Jenis --</option>
                                @foreach($barangTypeOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('barang_type') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- No Referensi --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">No Referensi</label>
                            <input type="text" class="form-control" name="no_referensi" placeholder="Cari No Referensi..." value="{{ request('no_referensi') }}">
                        </div>

                        {{-- Per Page --}}
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Tampilkan</label>
                            <select class="form-select" name="per_page">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>

                        {{-- Sort --}}
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Urutkan</label>
                            <select class="form-select" name="sort_by">
                                <option value="waktu_proses" {{ request('sort_by', 'waktu_proses') == 'waktu_proses' ? 'selected' : '' }}>Waktu</option>
                                <option value="jumlah" {{ request('sort_by') == 'jumlah' ? 'selected' : '' }}>Jumlah</option>
                                <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Order</label>
                            <select class="form-select" name="sort_order">
                                <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Terlama</option>
                            </select>
                        </div>

                        {{-- Buttons --}}
                        <div class="col-12">
                            <hr>
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('reports.gudangs.index') }}" class="btn btn-secondary">
                                    <i class="ri-refresh-line me-1"></i>Reset Filter
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-search-line me-1"></i>Terapkan Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-3 pb-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">
                        <i class="ri-table-line me-2"></i>Data History Gudang
                        <span class="badge bg-primary-subtle text-primary ms-2">{{ $historyGudang->total() }} Transaksi</span>
                    </h5>
                </div>
                <div class="col-auto">
                    <small class="text-muted">
                        Menampilkan {{ $historyGudang->firstItem() ?? 0 }} - {{ $historyGudang->lastItem() ?? 0 }} dari {{ $historyGudang->total() }} data
                    </small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="gudangTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Waktu Proses</th>
                            <th>Gudang</th>
                            <th>Nama Barang</th>
                            <th>Jenis Barang</th>
                            <th>Supplier</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Jumlah</th>
                            <th>No Referensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historyGudang as $index => $history)
                        <tr>
                            <td class="text-center">{{ $historyGudang->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ $history->waktu_proses->format('d M Y') }}</span>
                                    <small class="text-muted">{{ $history->waktu_proses->format('H:i') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">{{ $history->detailGudang->gudang->nama_gudang ?? '-' }}</span>
                                    <small class="text-muted">{{ $history->detailGudang->gudang->kode_gudang ?? '-' }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">{{ $history->detailGudang->barangObat->nama_obat_rs ?? '-' }}</span>
                                    @if($history->detailGudang && $history->detailGudang->no_batch)
                                        <small class="text-muted">Batch: {{ $history->detailGudang->no_batch }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info">
                                    {{ $history->detailGudang->barang_type ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if($history->supplier)
                                    <div class="d-flex flex-column">
                                        <span>{{ $history->supplier->nama_supplier }}</span>
                                        <small class="text-muted">{{ $history->supplier->no_telp ?? '-' }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($history->status == 'penerimaan')
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="ri-arrow-down-line me-1"></i>Penerimaan
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="ri-arrow-up-line me-1"></i>Pengiriman
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="fw-semibold {{ $history->status == 'penerimaan' ? 'text-success' : 'text-danger' }}">
                                    {{ $history->status == 'penerimaan' ? '+' : '-' }}{{ number_format($history->jumlah) }}
                                </span>
                            </td>
                            <td>
                                @if($history->no_referensi)
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ $history->no_referensi }}</span>
                                        <small class="text-muted">{{ $history->referensi_type ?? '-' }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ri-inbox-line fs-1 d-block mb-3"></i>
                                    <h5>Tidak ada data</h5>
                                    <p>Tidak ada data history gudang yang sesuai dengan filter Anda</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stats-card {
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }

    .avatar-lg {
        width: 4rem;
        height: 4rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .badge {
        font-weight: 500;
        padding: 0.5em 0.85em;
        font-size: 0.8125rem;
    }

    .card {
        border-radius: 12px;
    }

    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }

    @media print {
        .page-title-right,
        .btn,
        #filterCollapse,
        .pagination,
        .card-footer {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function () {
        $('#gudangTable').DataTable({
            paging: true,          // pagination tetap ada
            searching: false,      
            lengthChange: false,   
            info: false,           
            ordering: true,        
            pageLength: 10,        
            language: {
                paginate: {
                    previous: '<',
                    next: '>'
                }
            }
        });
    });
    // Export Function
    function exportData(type) {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        params.set('export', type);
        
        window.location.href = '{{ route("reports.gudangs.index") }}?' + params.toString();
    }

    // Print Function
    function printReport() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        params.set('print', 'true');
        
        const printWindow = window.open('{{ route("reports.gudangs.index") }}?' + params.toString(), '_blank');
        printWindow.onload = function() {
            printWindow.print();
        };
    }
</script>
@endpush