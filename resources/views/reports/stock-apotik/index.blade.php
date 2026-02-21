@extends('layouts.app')

@section('title', 'Laporan Stock Apotik')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Laporan Stock Apotik</li>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">
                    <i class="ri-medicine-bottle-line me-2"></i>Laporan Stock Apotik
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
                    <a href="{{ route('reports.stock-apotik.history') }}" class="btn btn-info ms-2">
                        <i class="ri-history-line me-1"></i>Riwayat Mutasi
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        {{-- Total Items --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-lg rounded-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <span class="avatar-title rounded-3">
                                    <i class="ri-medicine-bottle-line fs-2 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Item</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($statistics['total_items']) }}</h3>
                            <small class="text-muted">Produk Berbeda</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <div class="row text-center g-0">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block">Gudang</small>
                            <span class="fw-semibold text-primary">{{ number_format($statistics['total_gudang']) }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Transaksi</small>
                            <span class="fw-semibold text-info">{{ number_format($statistics['total_transaksi']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Stock --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-lg rounded-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <span class="avatar-title rounded-3">
                                    <i class="ri-stack-line fs-2 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Stock</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($statistics['total_stock']) }}</h3>
                            <small class="text-muted">Unit</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <div class="d-flex justify-content-between align-items-center px-2">
                        <small class="text-muted">Retur:</small>
                        <span class="fw-semibold text-danger">{{ number_format($statistics['total_retur']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stock Status --}}
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
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Status Stock</p>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($statistics['stock_aman']) }}</h3>
                            <small class="text-muted">Aman</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <div class="row text-center g-0">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block">Menipis</small>
                            <span class="fw-semibold text-warning">{{ number_format($statistics['stock_menipis']) }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Habis</small>
                            <span class="fw-semibold text-danger">{{ number_format($statistics['stock_habis']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kadaluarsa Status --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-0 shadow-sm overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-lg rounded-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <span class="avatar-title rounded-3">
                                    <i class="ri-calendar-close-line fs-2 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Kadaluarsa</p>
                            <h3 class="mb-0 fw-bold text-danger">{{ number_format($statistics['kadaluarsa']) }}</h3>
                            <small class="text-muted">Sudah Expired</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <div class="d-flex justify-content-between align-items-center px-2">
                        <small class="text-muted">Akan Expired (3bln):</small>
                        <span class="fw-semibold text-warning">{{ number_format($statistics['akan_kadaluarsa']) }}</span>
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
                <form action="{{ route('reports.stock-apotik.index') }}" method="GET" id="filterForm">
                    <div class="row g-3">
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

                        {{-- Nama Barang --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Nama Barang</label>
                            <input type="text" class="form-control" name="nama_barang" placeholder="Cari nama barang..." value="{{ request('nama_barang') }}">
                        </div>

                        {{-- No Batch --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">No Batch</label>
                            <input type="text" class="form-control" name="no_batch" placeholder="Cari no batch..." value="{{ request('no_batch') }}">
                        </div>

                        {{-- Kode Transaksi --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Kode Transaksi</label>
                            <input type="text" class="form-control" name="kode_transaksi" placeholder="Cari kode..." value="{{ request('kode_transaksi') }}">
                        </div>

                        {{-- Tanggal Penerimaan --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tanggal Penerimaan Dari</label>
                            <input type="date" class="form-control" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tanggal Penerimaan Sampai</label>
                            <input type="date" class="form-control" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
                        </div>

                        {{-- Tanggal Kadaluarsa --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Kadaluarsa Dari</label>
                            <input type="date" class="form-control" name="kadaluarsa_dari" value="{{ request('kadaluarsa_dari') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Kadaluarsa Sampai</label>
                            <input type="date" class="form-control" name="kadaluarsa_sampai" value="{{ request('kadaluarsa_sampai') }}">
                        </div>

                        {{-- Stock Range --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Stock Minimum</label>
                            <input type="number" class="form-control" name="stock_min" placeholder="Min..." value="{{ request('stock_min') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Stock Maximum</label>
                            <input type="number" class="form-control" name="stock_max" placeholder="Max..." value="{{ request('stock_max') }}">
                        </div>

                        {{-- Stock Status --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status Stock</label>
                            <select class="form-select" name="stock_status">
                                <option value="">-- Semua Status --</option>
                                <option value="habis" {{ request('stock_status') == 'habis' ? 'selected' : '' }}>Habis</option>
                                <option value="menipis" {{ request('stock_status') == 'menipis' ? 'selected' : '' }}>Menipis</option>
                                <option value="aman" {{ request('stock_status') == 'aman' ? 'selected' : '' }}>Aman</option>
                            </select>
                        </div>

                        {{-- Kadaluarsa Status --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status Kadaluarsa</label>
                            <select class="form-select" name="kadaluarsa_status">
                                <option value="">-- Semua --</option>
                                <option value="kadaluarsa" {{ request('kadaluarsa_status') == 'kadaluarsa' ? 'selected' : '' }}>Sudah Expired</option>
                                <option value="akan_kadaluarsa" {{ request('kadaluarsa_status') == 'akan_kadaluarsa' ? 'selected' : '' }}>Akan Expired (3 bulan)</option>
                                <option value="aman" {{ request('kadaluarsa_status') == 'aman' ? 'selected' : '' }}>Aman (>3 bulan)</option>
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
                                <option value="created_at" {{ request('sort_by', 'created_at') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                                <option value="tanggal_penerimaan" {{ request('sort_by') == 'tanggal_penerimaan' ? 'selected' : '' }}>Tgl Terima</option>
                                <option value="tanggal_kadaluarsa" {{ request('sort_by') == 'tanggal_kadaluarsa' ? 'selected' : '' }}>Tgl Expired</option>
                                <option value="stock_apotik" {{ request('sort_by') == 'stock_apotik' ? 'selected' : '' }}>Stock</option>
                                <option value="no_batch" {{ request('sort_by') == 'no_batch' ? 'selected' : '' }}>No Batch</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Order</label>
                            <select class="form-select" name="sort_order">
                                <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>Terbesar</option>
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Terkecil</option>
                            </select>
                        </div>

                        {{-- Buttons --}}
                        <div class="col-12">
                            <hr>
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('reports.stock-apotik.index') }}" class="btn btn-secondary">
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
    @if(request()->hasAny(['gudang_id', 'nama_barang', 'no_batch', 'kode_transaksi', 'stock_status', 'kadaluarsa_status']))
    <div class="mb-3">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="text-muted"><i class="ri-filter-line me-1"></i>Filter aktif:</span>
            
            @if(request('gudang_id'))
                @php $gudang = $gudangs->find(request('gudang_id')); @endphp
                <span class="badge bg-info-subtle text-info">
                    Gudang: {{ $gudang->nama_gudang ?? 'N/A' }}
                    <a href="{{ route('reports.stock-apotik.index', array_merge(request()->except('gudang_id'))) }}" class="text-info ms-1">×</a>
                </span>
            @endif

            @if(request('nama_barang'))
                <span class="badge bg-primary-subtle text-primary">
                    Nama: {{ request('nama_barang') }}
                    <a href="{{ route('reports.stock-apotik.index', array_merge(request()->except('nama_barang'))) }}" class="text-primary ms-1">×</a>
                </span>
            @endif

            @if(request('stock_status'))
                <span class="badge bg-success-subtle text-success">
                    Stock: {{ ucfirst(request('stock_status')) }}
                    <a href="{{ route('reports.stock-apotik.index', array_merge(request()->except('stock_status'))) }}" class="text-success ms-1">×</a>
                </span>
            @endif

            @if(request('kadaluarsa_status'))
                <span class="badge bg-warning-subtle text-warning">
                    Expired: {{ ucfirst(str_replace('_', ' ', request('kadaluarsa_status'))) }}
                    <a href="{{ route('reports.stock-apotik.index', array_merge(request()->except('kadaluarsa_status'))) }}" class="text-warning ms-1">×</a>
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
                        <i class="ri-table-line me-2"></i>Data Stock Apotik
                        <span class="badge bg-primary-subtle text-primary ms-2">{{ $stockApotiks->total() }} Item</span>
                    </h5>
                </div>
                <div class="col-auto">
                    <small class="text-muted">
                        Menampilkan {{ $stockApotiks->firstItem() ?? 0 }} - {{ $stockApotiks->lastItem() ?? 0 }} dari {{ $stockApotiks->total() }} data
                    </small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th style="width: 140px;">Kode Transaksi</th>
                            <th style="width: 120px;">Tanggal Terima</th>
                            <th style="width: 150px;">Gudang</th>
                            <th style="width: 220px;">Nama Barang</th>
                            <th style="width: 110px;">No Batch</th>
                            <th class="text-center" style="width: 100px;">Stock</th>
                            <th class="text-center" style="width: 80px;">Min</th>
                            <th class="text-center" style="width: 80px;">Retur</th>
                            <th style="width: 120px;">Kadaluarsa</th>
                            <th class="text-center" style="width: 120px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockApotiks as $index => $stock)
                        <tr>
                            <td class="text-center">{{ $stockApotiks->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-primary">{{ $stock->stockApotik->kode_transaksi ?? 'N/A' }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($stock->stockApotik->tanggal_penerimaan ?? now())->format('d M Y') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ \Carbon\Carbon::parse($stock->stockApotik->tanggal_penerimaan ?? now())->format('d M Y') }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($stock->stockApotik->tanggal_penerimaan ?? now())->diffForHumans() }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-2">
                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                            {{ substr($stock->stockApotik->gudang->nama_gudang ?? 'N', 0, 1) }}
                                        </span>
                                    </div>
                                    <span>{{ $stock->stockApotik->gudang->nama_gudang ?? 'N/A' }}</span>
                                </div>
                            </td>
                            
                            {{-- KOLOM NAMA BARANG --}}
                            <td>
                                <div class="product-item-wrapper">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <span class="d-block fw-medium text-dark" style="font-size: 0.875rem;">
                                                {{ $stock->detailObatRs->obatRs->nama_obat ?? $stock->nama_barang ?? 'N/A' }}
                                            </span>
                                            @if($stock->detailSupplier)
                                                <small class="text-muted d-block mt-1">
                                                    <i class="ri-price-tag-3-line me-1"></i>
                                                    @if($stock->detailSupplier->merk)
                                                        {{ $stock->detailSupplier->merk }}
                                                    @endif
                                                    @if($stock->detailSupplier->satuan)
                                                        - {{ $stock->detailSupplier->satuan }}
                                                    @endif
                                                </small>
                                            @endif
                                            @if($stock->detailObatRs->obatRs->product_code ?? false)
                                                <small class="text-muted d-block">
                                                    <i class="ri-barcode-line me-1"></i>{{ $stock->detailObatRs->obatRs->product_code }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">{{ $stock->no_batch }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $stockStatus = 'success';
                                    if ($stock->stock_apotik <= 0) $stockStatus = 'danger';
                                    elseif ($stock->stock_apotik <= $stock->min_persediaan) $stockStatus = 'warning';
                                @endphp
                                <span class="badge bg-{{ $stockStatus }}-subtle text-{{ $stockStatus }} fw-bold">
                                    {{ number_format($stock->stock_apotik) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <small class="text-muted">{{ number_format($stock->min_persediaan) }}</small>
                            </td>
                            <td class="text-center">
                                @if($stock->retur > 0)
                                    <span class="badge bg-danger-subtle text-danger">{{ number_format($stock->retur) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($stock->tanggal_kadaluarsa)
                                    @php
                                        $expDate = \Carbon\Carbon::parse($stock->tanggal_kadaluarsa);
                                        $today = \Carbon\Carbon::today();
                                        $diff = $today->diffInDays($expDate, false);
                                        
                                        if ($expDate->isPast()) {
                                            $badgeColor = 'danger';
                                            $icon = 'ri-close-circle-line';
                                        } elseif ($diff <= 90) {
                                            $badgeColor = 'warning';
                                            $icon = 'ri-error-warning-line';
                                        } else {
                                            $badgeColor = 'success';
                                            $icon = 'ri-checkbox-circle-line';
                                        }
                                    @endphp
                                    <div class="d-flex flex-column">
                                        <span class="text-{{ $badgeColor }}">
                                            <i class="{{ $icon }} me-1"></i>{{ $expDate->format('d M Y') }}
                                        </span>
                                        <small class="text-muted">
                                            @if($expDate->isPast())
                                                Expired {{ abs($diff) }} hari lalu
                                            @else
                                                {{ $diff }} hari lagi
                                            @endif
                                        </small>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-column gap-1">
                                    {{-- Stock Status Badge --}}
                                    @if($stock->stock_apotik <= 0)
                                        <span class="badge bg-danger-subtle text-danger">
                                            <i class="ri-close-circle-line me-1"></i>Habis
                                        </span>
                                    @elseif($stock->stock_apotik <= $stock->min_persediaan)
                                        <span class="badge bg-warning-subtle text-warning">
                                            <i class="ri-error-warning-line me-1"></i>Menipis
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="ri-checkbox-circle-line me-1"></i>Aman
                                        </span>
                                    @endif
                                    
                                    {{-- Expiry Status Badge --}}
                                    @if($stock->tanggal_kadaluarsa)
                                        @php
                                            $expDate = \Carbon\Carbon::parse($stock->tanggal_kadaluarsa);
                                            $diff = \Carbon\Carbon::today()->diffInDays($expDate, false);
                                        @endphp
                                        @if($expDate->isPast())
                                            <span class="badge bg-danger-subtle text-danger" style="font-size: 0.7rem;">
                                                <i class="ri-calendar-close-line me-1"></i>Expired
                                            </span>
                                        @elseif($diff <= 90)
                                            <span class="badge bg-warning-subtle text-warning" style="font-size: 0.7rem;">
                                                <i class="ri-calendar-line me-1"></i>{{ $diff }}d
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ri-inbox-line fs-1 d-block mb-3"></i>
                                    <h5>Tidak ada data</h5>
                                    <p>Tidak ada data stock apotik yang sesuai dengan filter Anda</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination --}}
        @if($stockApotiks->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <small class="text-muted">
                        Menampilkan {{ $stockApotiks->firstItem() }} - {{ $stockApotiks->lastItem() }} dari {{ $stockApotiks->total() }} data
                    </small>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end justify-content-center">
                        {{ $stockApotiks->links('pagination::bootstrap-5') }}
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

    .text-primary { color: #0d6efd !important; }
    .text-success { color: #198754 !important; }
    .text-info { color: #0dcaf0 !important; }
    .text-warning { color: #ffc107 !important; }
    .text-danger { color: #dc3545 !important; }
    .text-secondary { color: #6c757d !important; }

    .product-item-wrapper {
        min-width: 200px;
    }

    /* Print Styles */
    @media print {
        .page-title-right,
        .btn,
        #filterCollapse,
        .pagination,
        .card-footer {
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
        
        window.location.href = '{{ route("reports.stock-apotik.index") }}?' + params.toString();
    }

    // Print Function
    function printReport() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        params.set('print', 'true');
        
        const printWindow = window.open('{{ route("reports.stock-apotik.index") }}?' + params.toString(), '_blank');
        printWindow.onload = function() {
            printWindow.print();
        };
    }
</script>
@endpush