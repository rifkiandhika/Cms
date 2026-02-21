@extends('layouts.app')

@section('title', 'Riwayat Mutasi Stock Apotik')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.stock-apotik.index') }}">Laporan Stock Apotik</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Riwayat Mutasi</li>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">
                    <i class="ri-history-line me-2"></i>Riwayat Mutasi Stock Apotik
                </h4>
                <div class="page-title-right">
                    <a href="{{ route('reports.stock-apotik.index') }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i>Kembali ke Laporan
                    </a>
                    <button type="button" class="btn btn-outline-primary ms-2" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
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
        {{-- Total Penerimaan --}}
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <span class="avatar-title rounded">
                                    <i class="ri-arrow-down-circle-line text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Penerimaan</p>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['total_penerimaan']) }}</h5>
                            <small class="text-success">{{ number_format($stats['jumlah_penerimaan']) }} unit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <span class="avatar-title rounded">
                                    <i class="ri-arrow-up-circle-line text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Pengeluaran</p>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['total_pengeluaran']) }}</h5>
                            <small class="text-danger">{{ number_format($stats['jumlah_pengeluaran']) }} unit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Retur --}}
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <span class="avatar-title rounded">
                                    <i class="ri-arrow-go-back-line text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Retur</p>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['total_retur']) }}</h5>
                            <small class="text-warning">{{ number_format($stats['jumlah_retur']) }} unit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Transaksi --}}
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <span class="avatar-title rounded">
                                    <i class="ri-exchange-line text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total</p>
                            <h5 class="mb-0 fw-bold">{{ number_format($histories->total()) }}</h5>
                            <small class="text-info">Mutasi</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Net Change --}}
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                                <span class="avatar-title rounded">
                                    <i class="ri-line-chart-line text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Net Change</p>
                            @php
                                $netChange = $stats['jumlah_penerimaan'] - $stats['jumlah_pengeluaran'] - $stats['jumlah_retur'];
                            @endphp
                            <h5 class="mb-0 fw-bold {{ $netChange >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $netChange >= 0 ? '+' : '' }}{{ number_format($netChange) }}
                            </h5>
                            <small class="text-muted">unit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Period Info --}}
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                                <span class="avatar-title rounded">
                                    <i class="ri-calendar-line text-dark"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Periode</p>
                            <h6 class="mb-0 fw-bold">
                                @if(request('tanggal_dari') && request('tanggal_sampai'))
                                    Custom
                                @else
                                    Semua
                                @endif
                            </h6>
                            <small class="text-muted">
                                @if(request('tanggal_dari'))
                                    {{ \Carbon\Carbon::parse(request('tanggal_dari'))->format('d M') }} - 
                                    {{ request('tanggal_sampai') ? \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d M') : 'Sekarang' }}
                                @else
                                    Semua waktu
                                @endif
                            </small>
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
                <h5 class="mb-0"><i class="ri-filter-3-line me-2"></i>Filter Riwayat Mutasi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.stock-apotik.history') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        {{-- Status --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status Mutasi</label>
                            <select class="form-select" name="status">
                                <option value="">-- Semua Status --</option>
                                <option value="penerimaan" {{ request('status') == 'penerimaan' ? 'selected' : '' }}>
                                    Penerimaan
                                </option>
                                <option value="pengeluaran" {{ request('status') == 'pengeluaran' ? 'selected' : '' }}>
                                    Pengeluaran
                                </option>
                                <option value="retur" {{ request('status') == 'retur' ? 'selected' : '' }}>
                                    Retur
                                </option>
                            </select>
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

                        {{-- Tanggal Dari --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tanggal Dari</label>
                            <input type="date" class="form-control" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
                        </div>

                        {{-- Tanggal Sampai --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tanggal Sampai</label>
                            <input type="date" class="form-control" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
                        </div>

                        {{-- Per Page --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tampilkan</label>
                            <select class="form-select" name="per_page">
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                            </select>
                        </div>

                        {{-- Buttons --}}
                        <div class="col-12">
                            <hr>
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('reports.stock-apotik.history') }}" class="btn btn-secondary">
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
    @if(request()->hasAny(['status', 'gudang_id', 'tanggal_dari', 'tanggal_sampai']))
    <div class="mb-3">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="text-muted"><i class="ri-filter-line me-1"></i>Filter aktif:</span>
            
            @if(request('status'))
                <span class="badge bg-primary-subtle text-primary">
                    Status: {{ ucfirst(request('status')) }}
                    <a href="{{ route('reports.stock-apotik.history', array_merge(request()->except('status'))) }}" class="text-primary ms-1">×</a>
                </span>
            @endif

            @if(request('gudang_id'))
                @php $gudang = $gudangs->find(request('gudang_id')); @endphp
                <span class="badge bg-info-subtle text-info">
                    Gudang: {{ $gudang->nama_gudang ?? 'N/A' }}
                    <a href="{{ route('reports.stock-apotik.history', array_merge(request()->except('gudang_id'))) }}" class="text-info ms-1">×</a>
                </span>
            @endif

            @if(request('tanggal_dari'))
                <span class="badge bg-success-subtle text-success">
                    Dari: {{ \Carbon\Carbon::parse(request('tanggal_dari'))->format('d M Y') }}
                    <a href="{{ route('reports.stock-apotik.history', array_merge(request()->except('tanggal_dari'))) }}" class="text-success ms-1">×</a>
                </span>
            @endif

            @if(request('tanggal_sampai'))
                <span class="badge bg-warning-subtle text-warning">
                    Sampai: {{ \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d M Y') }}
                    <a href="{{ route('reports.stock-apotik.history', array_merge(request()->except('tanggal_sampai'))) }}" class="text-warning ms-1">×</a>
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
                        <i class="ri-list-check-2 me-2"></i>Riwayat Mutasi Stock
                        <span class="badge bg-primary-subtle text-primary ms-2">{{ $histories->total() }} Transaksi</span>
                    </h5>
                </div>
                <div class="col-auto">
                    <small class="text-muted">
                        Menampilkan {{ $histories->firstItem() ?? 0 }} - {{ $histories->lastItem() ?? 0 }} dari {{ $histories->total() }} data
                    </small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th style="width: 160px;">Waktu Proses</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 250px;">Nama Barang</th>
                            <th style="width: 130px;">No Batch</th>
                            <th style="width: 150px;">Gudang</th>
                            <th class="text-center" style="width: 100px;">Jumlah</th>
                            <th class="text-center" style="width: 100px;">Stock Awal</th>
                            <th class="text-center" style="width: 100px;">Stock Akhir</th>
                            <th style="width: 180px;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $index => $history)
                        <tr class="history-row">
                            <td class="text-center">{{ $histories->firstItem() + $index }}</td>
                            
                            {{-- Waktu Proses --}}
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($history->waktu_proses)->format('d M Y') }}</span>
                                    <small class="text-muted">
                                        <i class="ri-time-line me-1"></i>{{ \Carbon\Carbon::parse($history->waktu_proses)->format('H:i') }}
                                    </small>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($history->waktu_proses)->diffForHumans() }}</small>
                                </div>
                            </td>
                            
                            {{-- Status --}}
                            <td>
                                @php
                                    $statusConfig = [
                                        'penerimaan' => ['color' => 'success', 'icon' => 'ri-arrow-down-circle-line', 'text' => 'Penerimaan'],
                                        'pengeluaran' => ['color' => 'danger', 'icon' => 'ri-arrow-up-circle-line', 'text' => 'Pengeluaran'],
                                        'retur' => ['color' => 'warning', 'icon' => 'ri-arrow-go-back-line', 'text' => 'Retur'],
                                    ];
                                    $config = $statusConfig[$history->status] ?? ['color' => 'secondary', 'icon' => 'ri-question-line', 'text' => ucfirst($history->status)];
                                @endphp
                                <span class="badge bg-{{ $config['color'] }}-subtle text-{{ $config['color'] }}">
                                    <i class="{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                </span>
                            </td>
                            
                            {{-- Nama Barang --}}
                            <td>
                                <div class="product-info">
                                    <span class="d-block fw-medium text-dark">
                                        {{ $history->detailApotik->detailObatRs->obatRs->nama_obat ?? $history->detailApotik->nama_barang ?? 'N/A' }}
                                    </span>
                                    @if($history->detailApotik->detailSupplier)
                                        <small class="text-muted d-block mt-1">
                                            <i class="ri-price-tag-3-line me-1"></i>
                                            {{ $history->detailApotik->detailSupplier->merk ?? '' }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            
                            {{-- No Batch --}}
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $history->detailApotik->no_batch ?? 'N/A' }}
                                </span>
                            </td>
                            
                            {{-- Gudang --}}
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-2">
                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                            {{ substr($history->detailApotik->stockApotik->gudang->nama_gudang ?? 'N', 0, 1) }}
                                        </span>
                                    </div>
                                    <span class="small">{{ $history->detailApotik->stockApotik->gudang->nama_gudang ?? 'N/A' }}</span>
                                </div>
                            </td>
                            
                            {{-- Jumlah --}}
                            <td class="text-center">
                                @php
                                    $jumlahColor = match($history->status) {
                                        'penerimaan' => 'success',
                                        'pengeluaran' => 'danger',
                                        'retur' => 'warning',
                                        default => 'secondary'
                                    };
                                    $jumlahSign = match($history->status) {
                                        'penerimaan' => '+',
                                        'pengeluaran' => '-',
                                        'retur' => '-',
                                        default => ''
                                    };
                                @endphp
                                <span class="badge bg-{{ $jumlahColor }}-subtle text-{{ $jumlahColor }} fw-bold fs-6">
                                    {{ $jumlahSign }}{{ number_format($history->jumlah) }}
                                </span>
                            </td>
                            
                            {{-- Stock Awal --}}
                            <td class="text-center">
                                <span class="text-muted">{{ number_format($history->stock_awal) }}</span>
                            </td>
                            
                            {{-- Stock Akhir --}}
                            <td class="text-center">
                                <span class="fw-semibold text-primary">{{ number_format($history->stock_akhir) }}</span>
                                @php
                                    $diff = $history->stock_akhir - $history->stock_awal;
                                @endphp
                                <small class="d-block text-{{ $diff >= 0 ? 'success' : 'danger' }}">
                                    ({{ $diff >= 0 ? '+' : '' }}{{ number_format($diff) }})
                                </small>
                            </td>
                            
                            {{-- Keterangan --}}
                            <td>
                                @if($history->keterangan)
                                    <small class="text-muted d-block" style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                           title="{{ $history->keterangan }}">
                                        <i class="ri-information-line me-1"></i>{{ $history->keterangan }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ri-history-line fs-1 d-block mb-3"></i>
                                    <h5>Tidak ada riwayat</h5>
                                    <p>Tidak ada riwayat mutasi stock yang sesuai dengan filter Anda</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination --}}
        @if($histories->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <small class="text-muted">
                        Menampilkan {{ $histories->firstItem() }} - {{ $histories->lastItem() }} dari {{ $histories->total() }} data
                    </small>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end justify-content-center">
                        {{ $histories->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Timeline View (Optional Alternative View) --}}
    <div class="mt-4" style="display: none;" id="timelineView">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0"><i class="ri-time-line me-2"></i>Timeline Mutasi</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($histories as $history)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-{{ $statusConfig[$history->status]['color'] ?? 'secondary' }}"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">{{ $history->detailApotik->detailObatRs->obatRs->nama_obat ?? 'N/A' }}</h6>
                                    <span class="badge bg-{{ $statusConfig[$history->status]['color'] ?? 'secondary' }}-subtle text-{{ $statusConfig[$history->status]['color'] ?? 'secondary' }}">
                                        {{ $statusConfig[$history->status]['text'] ?? ucfirst($history->status) }}
                                    </span>
                                </div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($history->waktu_proses)->format('d M Y H:i') }}</small>
                            </div>
                            <p class="mb-1"><strong>Jumlah:</strong> {{ number_format($history->jumlah) }} unit</p>
                            <p class="mb-1"><strong>Stock:</strong> {{ number_format($history->stock_awal) }} → {{ number_format($history->stock_akhir) }}</p>
                            @if($history->keterangan)
                                <p class="mb-0 text-muted"><small>{{ $history->keterangan }}</small></p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
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

    .avatar-sm {
        width: 2.5rem;
        height: 2.5rem;
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

    .product-info {
        max-width: 250px;
    }

    /* History Row Animation */
    .history-row {
        animation: fadeInUp 0.3s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Timeline Styles */
    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 30px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }

    .timeline-item {
        position: relative;
        padding-left: 60px;
        margin-bottom: 30px;
    }

    .timeline-marker {
        position: absolute;
        left: 24px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px currentColor;
    }

    .timeline-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 3px solid #dee2e6;
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
            font-size: 0.7rem;
        }

        body {
            background: white !important;
        }

        .stats-card {
            break-inside: avoid;
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
        
        window.location.href = '{{ route("reports.stock-apotik.history") }}?' + params.toString();
    }

    // Print Function
    function printReport() {
        window.print();
    }

    // Toggle View (Table/Timeline)
    function toggleView(view) {
        const tableView = document.querySelector('.table-responsive').closest('.card');
        const timelineView = document.getElementById('timelineView');
        
        if (view === 'timeline') {
            tableView.style.display = 'none';
            timelineView.style.display = 'block';
        } else {
            tableView.style.display = 'block';
            timelineView.style.display = 'none';
        }
    }
</script>
@endpush