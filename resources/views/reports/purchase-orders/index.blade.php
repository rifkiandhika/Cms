@extends('layouts.app')

@section('title', 'Laporan Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Laporan PO</li>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">
                    <i class="ri-file-chart-line me-2"></i>Laporan Purchase Order
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
        {{-- Total PO --}}
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
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total PO</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($statistics['total_po']) }}</h3>
                            <small class="text-muted">Purchase Order</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <div class="row text-center g-0">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block">Internal</small>
                            <span class="fw-semibold text-primary">{{ number_format($statistics['internal']) }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Eksternal</small>
                            <span class="fw-semibold text-info">{{ number_format($statistics['eksternal']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Nilai --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-lg rounded-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <span class="avatar-title rounded-3">
                                    <i class="ri-money-dollar-circle-line fs-2 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Nilai</p>
                            <h3 class="mb-0 fw-bold">Rp {{ number_format($statistics['total_nilai'], 0, ',', '.') }}</h3>
                            <small class="text-muted">Semua PO</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <div class="row text-center g-0">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block">Internal</small>
                            <span class="fw-semibold text-primary">Rp {{ number_format($statistics['nilai_internal'] / 1000000, 1) }}jt</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Eksternal</small>
                            <span class="fw-semibold text-info">Rp {{ number_format($statistics['nilai_eksternal'] / 1000000, 1) }}jt</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Progress --}}
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
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Status</p>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($statistics['diterima']) }}</h3>
                            <small class="text-muted">Diterima</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <div class="row text-center g-0">
                        <div class="col-4 border-end">
                            <small class="text-muted d-block">Proses</small>
                            <span class="fw-semibold text-warning">{{ number_format($statistics['dalam_proses']) }}</span>
                        </div>
                        <div class="col-4 border-end">
                            <small class="text-muted d-block">Tolak</small>
                            <span class="fw-semibold text-danger">{{ number_format($statistics['ditolak']) }}</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Batal</small>
                            <span class="fw-semibold text-secondary">{{ number_format($statistics['dibatalkan']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Outstanding --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-lg rounded-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <span class="avatar-title rounded-3">
                                    <i class="ri-wallet-3-line fs-2 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Outstanding</p>
                            <h3 class="mb-0 fw-bold text-warning">Rp {{ number_format($statistics['total_outstanding'], 0, ',', '.') }}</h3>
                            <small class="text-muted">Belum diterima</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Sudah diterima:</small>
                        <span class="fw-semibold text-success">Rp {{ number_format($statistics['total_diterima'] / 1000000, 1) }}jt</span>
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
                <form action="{{ route('reports.purchase-orders.index') }}" method="GET" id="filterForm">
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

                        {{-- No PO --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">No PO</label>
                            <input type="text" class="form-control" name="no_po" placeholder="Cari No PO..." value="{{ request('no_po') }}">
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

                        {{-- Tipe PO --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tipe PO</label>
                            <select class="form-select" name="tipe_po">
                                <option value="">-- Semua Tipe --</option>
                                <option value="internal" {{ request('tipe_po') == 'internal' ? 'selected' : '' }}>Internal</option>
                                <option value="eksternal" {{ request('tipe_po') == 'eksternal' ? 'selected' : '' }}>Eksternal</option>
                            </select>
                        </div>

                        {{-- Unit Pemohon --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Unit Pemohon</label>
                            <select class="form-select" name="unit_pemohon">
                                <option value="">-- Semua Unit --</option>
                                <option value="apotik" {{ request('unit_pemohon') == 'apotik' ? 'selected' : '' }}>Apotik</option>
                                <option value="gudang" {{ request('unit_pemohon') == 'gudang' ? 'selected' : '' }}>Gudang</option>
                            </select>
                        </div>

                        {{-- Supplier --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Supplier</label>
                            <select class="form-select" name="id_supplier">
                                <option value="">-- Semua Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id_supplier }}" {{ request('id_supplier') == $supplier->id_supplier ? 'selected' : '' }}>
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pemohon --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Pemohon</label>
                            <select class="form-select" name="id_karyawan_pemohon">
                                <option value="">-- Semua Pemohon --</option>
                                @foreach($karyawans as $karyawan)
                                    <option value="{{ $karyawan->id_karyawan }}" {{ request('id_karyawan_pemohon') == $karyawan->id_karyawan ? 'selected' : '' }}>
                                        {{ $karyawan->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Grand Total Range --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Grand Total Min</label>
                            <input type="number" class="form-control" name="grand_total_min" placeholder="Minimum..." value="{{ request('grand_total_min') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Grand Total Max</label>
                            <input type="number" class="form-control" name="grand_total_max" placeholder="Maximum..." value="{{ request('grand_total_max') }}">
                        </div>

                        {{-- Approval Status --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status Approval Kepala Gudang</label>
                            <select class="form-select" name="status_approval_kepala_gudang">
                                <option value="">-- Semua --</option>
                                <option value="pending" {{ request('status_approval_kepala_gudang') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="disetujui" {{ request('status_approval_kepala_gudang') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ request('status_approval_kepala_gudang') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status Approval Kasir</label>
                            <select class="form-select" name="status_approval_kasir">
                                <option value="">-- Semua --</option>
                                <option value="pending" {{ request('status_approval_kasir') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="disetujui" {{ request('status_approval_kasir') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ request('status_approval_kasir') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
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
                                <option value="tanggal_permintaan" {{ request('sort_by', 'tanggal_permintaan') == 'tanggal_permintaan' ? 'selected' : '' }}>Tanggal</option>
                                <option value="no_po" {{ request('sort_by') == 'no_po' ? 'selected' : '' }}>No PO</option>
                                <option value="grand_total" {{ request('sort_by') == 'grand_total' ? 'selected' : '' }}>Grand Total</option>
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
                                <a href="{{ route('reports.purchase-orders.index') }}" class="btn btn-secondary">
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

    {{-- Active Filters --}}
    @if(request()->hasAny(['tanggal_dari', 'tanggal_sampai', 'status', 'tipe_po', 'id_supplier', 'no_po', 'id_karyawan_pemohon', 'unit_pemohon', 'grand_total_min', 'grand_total_max']))
    <div class="mb-3">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="text-muted"><i class="ri-filter-line me-1"></i>Filter aktif:</span>
            
            @if(request('tanggal_dari'))
                <span class="badge bg-info-subtle text-info">
                    Dari: {{ date('d/m/Y', strtotime(request('tanggal_dari'))) }}
                    <a href="{{ route('reports.purchase-orders.index', array_merge(request()->except('tanggal_dari'))) }}" class="text-info ms-1">×</a>
                </span>
            @endif

            @if(request('tanggal_sampai'))
                <span class="badge bg-info-subtle text-info">
                    Sampai: {{ date('d/m/Y', strtotime(request('tanggal_sampai'))) }}
                    <a href="{{ route('reports.purchase-orders.index', array_merge(request()->except('tanggal_sampai'))) }}" class="text-info ms-1">×</a>
                </span>
            @endif

            @if(request('status'))
                <span class="badge bg-primary-subtle text-primary">
                    Status: {{ $statusOptions[request('status')] }}
                    <a href="{{ route('reports.purchase-orders.index', array_merge(request()->except('status'))) }}" class="text-primary ms-1">×</a>
                </span>
            @endif

            @if(request('tipe_po'))
                <span class="badge bg-success-subtle text-success">
                    Tipe: {{ ucfirst(request('tipe_po')) }}
                    <a href="{{ route('reports.purchase-orders.index', array_merge(request()->except('tipe_po'))) }}" class="text-success ms-1">×</a>
                </span>
            @endif

            @if(request('no_po'))
                <span class="badge bg-warning-subtle text-warning">
                    No PO: {{ request('no_po') }}
                    <a href="{{ route('reports.purchase-orders.index', array_merge(request()->except('no_po'))) }}" class="text-warning ms-1">×</a>
                </span>
            @endif
        </div>
    </div>
    @endif

    {{-- Data Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-3 pb-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">
                        <i class="ri-table-line me-2"></i>Data Purchase Order
                        <span class="badge bg-primary-subtle text-primary ms-2">{{ $purchaseOrders->total() }} PO</span>
                    </h5>
                </div>
                <div class="col-auto">
                    <small class="text-muted">
                        Menampilkan {{ $purchaseOrders->firstItem() ?? 0 }} - {{ $purchaseOrders->lastItem() ?? 0 }} dari {{ $purchaseOrders->total() }} data
                    </small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0" id="poTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th style="width: 140px;">No PO</th>
                            <th style="width: 120px;">Tanggal</th>
                            <th class="text-center" style="width: 80px;">Tipe</th>
                            <th style="width: 150px;">Pemohon</th>
                            <th style="width: 180px;">Supplier/Tujuan</th>
                            <th style="width: 220px;">Nama Barang</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                            <th class="text-end" style="width: 130px;">Grand Total</th>
                            <th class="text-center" style="width: 130px;">Diterima</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $index => $po)
                        <tr>
                            <td class="text-center">{{ $purchaseOrders->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-primary">{{ $po->no_po }}</span>
                                    <small class="text-muted">{{ $po->unit_pemohon }} → {{ $po->unit_tujuan ?? 'N/A' }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ \Carbon\Carbon::parse($po->tanggal_permintaan)->format('d M Y') }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($po->tanggal_permintaan)->diffForHumans() }}</small>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($po->tipe_po == 'internal')
                                    <span class="badge bg-primary-subtle text-primary">
                                        <i class="ri-building-line me-1"></i>Internal
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info">
                                        <i class="ri-global-line me-1"></i>Eksternal
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-2">
                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                            {{ substr($po->karyawanPemohon->nama_lengkap ?? 'N', 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="d-block">{{ $po->karyawanPemohon->nama_lengkap ?? 'N/A' }}</span>
                                        <small class="text-muted">{{ ucfirst($po->unit_pemohon) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($po->supplier)
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ $po->supplier->nama_supplier }}</span>
                                        <small class="text-muted">{{ $po->supplier->no_telp ?? '-' }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">{{ ucfirst($po->unit_tujuan ?? 'N/A') }}</span>
                                @endif
                            </td>
                            
                            {{-- KOLOM NAMA BARANG --}}
                            <td>
                                @if($po->items->count() > 0)
                                    <div class="product-items-wrapper">
                                        {{-- Item pertama selalu ditampilkan --}}
                                        <div class="first-product-item mb-1">
                                            <div class="d-flex align-items-start">
                                                <span class="badge bg-secondary-subtle text-secondary me-2" style="font-size: 0.7rem;">1</span>
                                                <div class="flex-grow-1">
                                                    <span class="d-block fw-medium text-dark" style="font-size: 0.875rem;">
                                                        {{ Str::limit($po->items->first()->produk->nama ?? 'N/A', 40) }}
                                                    </span>
                                                    <small class="text-muted">
                                                        <i class="ri-shopping-cart-line me-1"></i>Qty: {{ number_format($po->items->first()->qty_diminta) }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        {{-- Jika lebih dari 1 item, tampilkan dropdown --}}
                                        @if($po->items->count() > 1)
                                            <div class="dropdown more-items-dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100" 
                                                        type="button" 
                                                        id="dropdownItems{{ $po->id_po }}" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false"
                                                        style="font-size: 0.75rem; padding: 0.35rem 0.6rem; border-style: dashed;">
                                                    <i class="ri-list-check me-1"></i>
                                                    +{{ $po->items->count() - 1 }} item lainnya
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-start shadow-sm" 
                                                    aria-labelledby="dropdownItems{{ $po->id_po }}" 
                                                    style="max-height: 350px; overflow-y: auto; min-width: 320px;">
                                                    @foreach($po->items->skip(1) as $item)
                                                    <li>
                                                        <div class="dropdown-item-text px-3 py-2">
                                                            <div class="d-flex align-items-start">
                                                                <span class="badge bg-secondary-subtle text-secondary me-2" style="font-size: 0.7rem;">
                                                                    {{ $loop->iteration + 1 }}
                                                                </span>
                                                                <div class="flex-grow-1">
                                                                    <span class="d-block fw-medium text-dark mb-1" style="font-size: 0.875rem;">
                                                                        {{ $item->produk->nama ?? 'N/A' }}
                                                                    </span>
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <small class="text-muted">
                                                                            <i class="ri-shopping-cart-line me-1"></i>
                                                                            Qty: {{ number_format($item->qty_diminta) }}
                                                                        </small>
                                                                        <small class="text-primary fw-semibold">
                                                                            Rp {{ number_format($item->harga_satuan * $item->qty_diminta, 0, ',', '.') }}
                                                                        </small>
                                                                    </div>
                                                                    @if($item->produk->product_code ?? false)
                                                                        <small class="text-muted d-block mt-1">
                                                                            <i class="ri-barcode-line me-1"></i>{{ $item->produk->product_code }}
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    @if(!$loop->last)
                                                    <li><hr class="dropdown-divider my-1"></li>
                                                    @endif
                                                    @endforeach
                                                    
                                                    {{-- Footer dropdown dengan total --}}
                                                    <li><hr class="dropdown-divider my-1"></li>
                                                    <li>
                                                        <div class="dropdown-item-text px-3 py-2 bg-light">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <small class="fw-semibold text-dark">
                                                                    <i class="ri-shopping-basket-line me-1"></i>
                                                                    Total {{ $po->items->count() }} Item
                                                                </small>
                                                                <small class="fw-bold text-primary">
                                                                    Rp {{ number_format($po->items->sum(function($item) { 
                                                                        return $item->harga_satuan * $item->qty_diminta; 
                                                                    }), 0, ',', '.') }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            
                            <td class="text-center">
                                @php
                                    $statusConfig = [
                                        'draft' => ['color' => 'secondary', 'icon' => 'ri-draft-line'],
                                        'menunggu_persetujuan_kepala_gudang' => ['color' => 'warning', 'icon' => 'ri-time-line'],
                                        'menunggu_persetujuan_kasir' => ['color' => 'warning', 'icon' => 'ri-time-line'],
                                        'disetujui' => ['color' => 'success', 'icon' => 'ri-checkbox-circle-line'],
                                        'dikirim_ke_supplier' => ['color' => 'info', 'icon' => 'ri-send-plane-line'],
                                        'dalam_pengiriman' => ['color' => 'info', 'icon' => 'ri-truck-line'],
                                        'diterima' => ['color' => 'success', 'icon' => 'ri-check-double-line'],
                                        'ditolak' => ['color' => 'danger', 'icon' => 'ri-close-circle-line'],
                                        'dibatalkan' => ['color' => 'dark', 'icon' => 'ri-close-line']
                                    ];
                                    $config = $statusConfig[$po->status] ?? ['color' => 'secondary', 'icon' => 'ri-question-line'];
                                @endphp
                                <span class="badge bg-{{ $config['color'] }}-subtle text-{{ $config['color'] }}">
                                    <i class="{{ $config['icon'] }} me-1"></i>
                                    {{ str_replace('_', ' ', ucwords($po->status, '_')) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</span>
                                    <small class="text-muted">{{ $po->items->count() }} item</small>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($po->grand_total_diterima)
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-success">Rp {{ number_format($po->grand_total_diterima, 0, ',', '.') }}</span>
                                        <small class="text-muted">
                                            {{ number_format(($po->grand_total_diterima / $po->grand_total) * 100, 1) }}%
                                        </small>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ri-inbox-line fs-1 d-block mb-3"></i>
                                    <h5>Tidak ada data</h5>
                                    <p>Tidak ada data purchase order yang sesuai dengan filter Anda</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination --}}
            @if($purchaseOrders->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <small class="text-muted">
                            Menampilkan {{ $purchaseOrders->firstItem() }} - {{ $purchaseOrders->lastItem() }} dari {{ $purchaseOrders->total() }} data
                        </small>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end justify-content-center">
                            {{ $purchaseOrders->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .pagination {
        margin: 0;
        gap: 0.25rem;
    }

    .pagination .page-item {
        margin: 0 2px;
    }

    .pagination .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        color: #6c757d;
        transition: all 0.2s ease;
    }

    .pagination .page-link:hover {
        background-color: #f8f9fa;
        border-color: #0d6efd;
        color: #0d6efd;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
        font-weight: 600;
    }

    .pagination .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Compact pagination for better spacing */
    .card-footer .pagination {
        flex-wrap: wrap;
    }

    .card-footer .d-flex {
        align-items: center;
        gap: 1rem;
    }
    
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

    .avatar-xs {
        width: 2rem;
        height: 2rem;
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

    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
        padding: 12px 8px;
        white-space: nowrap;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .table tbody td {
        padding: 12px 8px;
        vertical-align: middle;
    }

    .badge {
        font-weight: 500;
        padding: 0.5em 0.85em;
        font-size: 0.8125rem;
    }

    .card {
        border-radius: 12px;
    }

    .form-label {
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        font-size: 0.875rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.5rem 1rem;
    }

    .page-title-box {
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }

    /* Badge Subtle Colors */
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
    .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
    .bg-dark-subtle { background-color: rgba(33, 37, 41, 0.1) !important; }

    .text-primary { color: #0d6efd !important; }
    .text-success { color: #198754 !important; }
    .text-info { color: #0dcaf0 !important; }
    .text-warning { color: #ffc107 !important; }
    .text-danger { color: #dc3545 !important; }
    .text-secondary { color: #6c757d !important; }
    .text-dark { color: #212529 !important; }

    /* Product Items Styling */
    .product-items-wrapper {
        min-width: 200px;
    }

    .first-product-item {
        background: #f8f9fa;
        padding: 8px;
        border-radius: 6px;
        border-left: 3px solid #0d6efd;
    }

    .more-items-dropdown .dropdown-toggle {
        transition: all 0.2s ease;
    }

    .more-items-dropdown .dropdown-toggle:hover {
        background-color: #f8f9fa;
        border-color: #0d6efd;
        color: #0d6efd;
    }

    .more-items-dropdown .dropdown-menu {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .more-items-dropdown .dropdown-item-text {
        white-space: normal;
        cursor: default;
        transition: background-color 0.2s ease;
    }

    .more-items-dropdown .dropdown-item-text:hover {
        background-color: #f8f9fa;
    }

    .more-items-dropdown .dropdown-divider {
        margin: 0;
    }

    /* Scrollbar styling untuk dropdown */
    .more-items-dropdown .dropdown-menu::-webkit-scrollbar {
        width: 6px;
    }

    .more-items-dropdown .dropdown-menu::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .more-items-dropdown .dropdown-menu::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .more-items-dropdown .dropdown-menu::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Print Styles */
    @media print {
        .page-title-right,
        .btn,
        .dropdown,
        #filterCollapse,
        .pagination,
        .card-footer,
        .more-items-dropdown {
            display: none !important;
        }

        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }

        .table {
            font-size: 0.75rem;
        }

        body {
            background: white !important;
        }

        .first-product-item {
            background: white !important;
            border: 1px solid #dee2e6 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Export Function
    function exportData(type) {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        params.set('export', type);
        
        window.location.href = '{{ route("reports.purchase-orders.index") }}?' + params.toString();
    }

    // Print Function
    function printReport() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        params.set('print', 'true');
        
        const printWindow = window.open('{{ route("reports.purchase-orders.index") }}?' + params.toString(), '_blank');
        printWindow.onload = function() {
            printWindow.print();
        };
    }

    // Initialize tooltips if needed
    // document.addEventListener('DOMContentLoaded', function() {
    //     // Auto close dropdowns when clicking outside
    //     document.addEventListener('click', function(e) {
    //         if (!e.target.closest('.dropdown')) {
    //             const dropdowns = document.querySelectorAll('.dropdown-menu.show');
    //             dropdowns.forEach(dropdown => {
    //                 dropdown.classList.remove('show');
    //             });
    //         }
    //     });
    // });
</script>
@endpush