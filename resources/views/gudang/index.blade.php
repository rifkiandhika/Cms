@extends('layouts.app')

@section('title', 'Gudang')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Gudang</li>
@endsection

@section('content')
   <div class="app-body">
        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-checkbox-circle-line me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ri-error-warning-line me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-primary bg-soft">
                                    <span class="avatar-title rounded-circle bg-primary bg-gradient">
                                        <i class="ri-store-2-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">Total Gudang</p>
                                <h4 class="mb-0">{{ $gudangs->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-success bg-soft">
                                    <span class="avatar-title rounded-circle bg-success bg-gradient">
                                        <i class="ri-truck-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">Total Supplier</p>
                                <h4 class="mb-0">{{ $gudangs->pluck('supplier')->unique()->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-info bg-soft">
                                    <span class="avatar-title rounded-circle bg-info bg-gradient">
                                        <i class="ri-box-3-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">Total Barang</p>
                                <h4 class="mb-0">{{ $gudangs->sum(function($g) { return $g->details->count(); }) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-warning bg-soft">
                                    <span class="avatar-title rounded-circle bg-warning bg-gradient">
                                        <i class="ri-alert-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">Stok Rendah</p>
                                <h4 class="mb-0">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="ri-list-check-2 me-2"></i>Daftar Gudang
                            </h5>
                            <a class="btn btn-primary btn-sm" href="{{ route('gudangs.create') }}">
                                <i class="ri-add-circle-line me-1"></i>Tambah Barang
                            </a>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="card-body border-bottom bg-light">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Filter Supplier</label>
                                <select class="form-select form-select-sm" id="filterSupplier">
                                    <option value="">Semua Supplier</option>
                                    @foreach($gudangs->pluck('supplier.nama_supplier')->unique()->filter() as $supplier)
                                        <option value="{{ $supplier }}">{{ $supplier }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Cari</label>
                                <input type="text" class="form-control form-control-sm" id="searchBox" placeholder="Cari supplier atau barang...">
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle" id="gudangTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50" class="text-center">No</th>
                                        <th>Kode Gudang</th>
                                        <th>Kategori Obat</th>
                                        {{-- <th width="150">Jumlah Barang</th> --}}
                                        <th>Tanggal Dibuat</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($gudangs as $x => $data)
                                        <tr>
                                            <td class="text-center">{{ $x + 1 }}</td>
                                            <td><strong>{{ $data->kode_gudang }}</strong></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                            {{ substr($data->nama_gudang ?? 'N', 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $data->nama_gudang ?? '-' }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            {{-- <td class="text-center">
                                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $data->id }}">
                                                    <i class="ri-eye-line me-1"></i>
                                                    Lihat ({{ $data->details->count() }})
                                                </button>
                                            </td> --}}
                                            <td>
                                                <small class="text-muted">
                                                    <i class="ri-calendar-line"></i> {{ $data->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                @php
                                                    $badge = match($data->status) {
                                                        'Aktif' => 'bg-success',
                                                        'Nonaktif' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp

                                                <span class="badge-status {{ $data->status == 'Aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                                                    {{ $data->status }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-2-fill"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('gudangs.show', $data->id) }}">
                                                                <i class="ri-eye-line me-2"></i>Detail
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('gudangs.edit', $data->id) }}">
                                                                <i class="ri-pencil-fill me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        {{-- <li>
                                                            <button type="button" 
                                                                    class="dropdown-item"
                                                                    onclick="window.location.href='{{ route('gudang.history', $data->id) }}'">
                                                                <i class="ri-time-line me-2"></i>History
                                                            </button>
                                                        </li> --}}
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('gudangs.destroy', $data->id) }}" method="POST" class="delete-confirm">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="ri-delete-bin-6-line me-2"></i>Hapus
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <i class="ri-inbox-line ri-3x text-muted d-block mb-3"></i>
                                                <p class="text-muted mb-0">Belum ada data gudang</p>
                                                <a href="{{ route('gudangs.create') }}" class="btn btn-primary btn-sm mt-3">
                                                    <i class="ri-add-circle-line me-1"></i>Tambah Barang
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- @foreach($gudangs as $data)
        <!-- Modal Detail - Simple Table View (Like Screenshot) -->
        <div class="modal fade" id="detailModal{{ $data->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $data->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 65%;">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-primary text-white">
                        <div>
                            <h5 class="modal-title mb-1">
                                <i class="ri-file-list-3-line me-2"></i>Daftar Laporan Produksi
                            </h5>
                            <p class="mb-0 small opacity-75">{{ $data->supplier->nama_supplier ?? 'N/A' }}</p>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <!-- Enhanced Filter Bar -->
                    <div class="modal-body bg-light border-bottom p-3">
                        <div class="row g-2">
                            <div class="col-md-12">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control search-detail-{{ $data->id }}" 
                                           placeholder="Pencarian/filter...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-success flex-fill" title="Tambah (Web Form)">
                                        <i class="ri-add-line me-1"></i>Tambah (Web Form)
                                    </button>
                                    <button class="btn btn-sm btn-primary" title="Tambah (Excel)">
                                        <i class="ri-file-excel-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Content -->
                    <div class="modal-body p-0" style="max-height: 65vh; overflow-y: auto;">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle mb-0" id="detailTable-{{ $data->id }}" style="font-size: 0.875rem;">
                                <thead class="table-light sticky-top" style="top: 0; z-index: 10;">
                                    <tr>
                                        <th width="50" class="text-center" style="background-color: #f8f9fa;">No.</th>
                                        <th width="200" style="background-color: #f8f9fa;">ID PRODUK</th>
                                        <th style="background-color: #f8f9fa;">PRODUK</th>
                                        <th width="220" style="background-color: #f8f9fa;">STOK</th>
                                    </tr>
                                </thead>
                                <tbody id="detailTableBody-{{ $data->id }}">
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="ri-loader-4-line ri-spin ri-2x text-primary"></i>
                                            <p class="text-muted mt-2 mb-0">Memuat data...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="modal-footer bg-light">
                        <div class="me-auto text-muted small">
                            <span id="total-items-{{ $data->id }}">Total: 0 data</span>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach --}}
@endsection

@push('styles')
    <style>
       .badge-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;

            padding: 4px 10px;
            border-radius: 999px;

            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.3px;

            white-space: nowrap;
            line-height: 1;
        }

        /* ================= STATUS ================= */

        /* Aktif */
        .badge-aktif {
            background: #DCFCE7;     /* soft green */
            color: #166534;          /* dark green */
        }

        /* Nonaktif */
        .badge-nonaktif {
            background: #FEE2E2;     /* soft red */
            color: #991B1B;          /* dark red */
        }

        /* Optional kalau mau tetap pakai yang lama */
        /* .badge-normal   { background: #DCFCE7; color: #166534; }
        .badge-menipis  { background: #FEF3C7; color: #92400E; }
        .badge-expired  { background: #FEE2E2; color: #991B1B; }
        .badge-warning  { background: #EDE9FE; color: #5B21B6; } */

        .badge {
            font-weight: 500;
            padding: 0.4em 0.8em;
            font-size: 0.85rem;
        }
        
        .avatar-xs {
            height: 2rem;
            width: 2rem;
        }
        
        .avatar-sm {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .avatar-title {
            align-items: center;
            display: flex;
            font-weight: 600;
            height: 100%;
            justify-content: center;
            width: 100%;
        }
        
        .bg-soft {
            opacity: 0.1;
        }
        
        .bg-soft-primary {
            background-color: rgba(13, 110, 253, 0.15) !important;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }
        
        .card {
            border-radius: 0.5rem;
        }

        .dropdown-item i {
            width: 20px;
        }
        
        .cursor {
            cursor: pointer;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        /* Modal Table Styles - Matching Screenshot */
        #detailTable-{{ $data->id ?? 'default' }} {
            border-collapse: collapse;
        }

        #detailTable-{{ $data->id ?? 'default' }} thead th {
            background-color: #f8f9fa !important;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #dee2e6;
            padding: 12px 15px;
            color: #6c757d;
        }

        #detailTable-{{ $data->id ?? 'default' }} tbody td {
            font-size: 0.813rem;
            padding: 12px 15px;
            vertical-align: top;
            border: 1px solid #dee2e6;
            line-height: 1.6;
        }

        #detailTable-{{ $data->id ?? 'default' }} tbody tr:hover {
            background-color: #f8f9fa;
        }

        .sticky-top {
            position: sticky;
            background-color: #f8f9fa;
        }

        /* Product Info Styles */
        .product-info-line {
            margin-bottom: 4px;
            color: #495057;
        }

        .product-info-line:last-child {
            margin-bottom: 0;
        }

        .product-label {
            color: #6c757d;
            font-size: 0.75rem;
        }

        .product-value {
            color: #212529;
            font-weight: 500;
        }

        .stock-info-line {
            margin-bottom: 4px;
            color: #495057;
        }

        .stock-info-line:last-child {
            margin-bottom: 0;
        }

        /* Custom scrollbar for modal */
        .modal-body::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .empty-state-table {
            text-align: center;
            padding: 40px 20px;
        }
    </style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let dataTable = null;
    
    // Function to initialize DataTable
    function initializeDataTable() {
        const $table = $('#gudangTable');
        
        // Check if table has data
        const isEmpty = $table.find('tbody tr td[colspan]').length > 0;
        
        // Destroy existing DataTable if exists
        if ($.fn.DataTable.isDataTable('#gudangTable')) {
            $table.DataTable().destroy();
        }
        
        // Only initialize DataTable if table has data
        if (!isEmpty) {
            try {
                dataTable = $table.DataTable({
                    responsive: true,
                    pageLength: 10,
                    order: [[0, 'asc']],
                    dom: 'rtip',
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                        emptyTable: "Belum ada data gudang"
                    },
                    columnDefs: [
                        { orderable: false, targets: [3] }
                    ]
                });

                // Custom search
                $('#searchBox').on('keyup', function() {
                    dataTable.search(this.value).draw();
                });

                // Filter by supplier
                $('#filterSupplier').on('change', function() {
                    dataTable.column(1).search(this.value).draw();
                });
            } catch (error) {
                console.log('DataTable initialization skipped - no data available');
            }
        }
    }
    
    // Initialize DataTable on page load
    initializeDataTable();

    // Confirm delete with SweetAlert
    $(document).on('submit', '.delete-confirm', function(e) {
        e.preventDefault();
        var form = this;
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data gudang akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Auto dismiss alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // ========== Detail Modal Script - Simple Table View ===============
    // let loadedModals = {};

    // $(document).on('show.bs.modal', '[id^="detailModal"]', function () {
    //     const modal = $(this);
    //     const gudangId = modal.attr('id').replace('detailModal', '');
    //     const tableBodyId = `detailTableBody-${gudangId}`;
    //     const $tableBody = $(`#${tableBodyId}`);

    //     // Check if already loaded
    //     if (loadedModals[gudangId]) {
    //         return;
    //     }

    //     console.log('Loading data for gudang:', gudangId);

    //     // Fetch data via AJAX
    //     $.ajax({
    //         url: `/gudang/${gudangId}/details/data`,
    //         type: 'GET',
    //         data: {
    //             start: 0,
    //             length: -1 // Get all data
    //         },
    //         success: function(response) {
    //             console.log('Data received:', response);
                
    //             if (!response.data || response.data.length === 0) {
    //                 $tableBody.html(`
    //                     <tr>
    //                         <td colspan="4" class="empty-state-table">
    //                             <i class="ri-inbox-line ri-3x text-muted d-block mb-3"></i>
    //                             <p class="text-muted mb-0">Tidak ada data barang</p>
    //                         </td>
    //                     </tr>
    //                 `);
    //                 $(`#total-items-${gudangId}`).text('Total: 0 data');
    //                 return;
    //             }

    //             // Build table rows HTML
    //             let rowsHtml = '';
    //             response.data.forEach((item, index) => {
    //                 rowsHtml += `
    //                     <tr>
    //                         <td class="text-center">${index + 1}</td>
    //                         <td>${item.kode_produk || '-'}</td>
    //                         <td>
    //                             <div class="product-info-line">
    //                                 <span class="product-label">Nama :</span> 
    //                                 <span class="product-value">${item.nama_dagangan || '-'}</span>
    //                             </div>
    //                             ${item.nomor_izin_edar ? `
    //                             <div class="product-info-line">
    //                                 <span class="product-label">NIE :</span> 
    //                                 <span class="product-value">${item.nomor_izin_edar}</span>
    //                             </div>
    //                             ` : ''}
    //                             ${item.tipe ? `
    //                             <div class="product-info-line">
    //                                 <span class="product-label">Tipe :</span> 
    //                                 <span class="product-value">${item.tipe}</span>
    //                             </div>
    //                             ` : ''}
    //                             ${item.ukuran ? `
    //                             <div class="product-info-line">
    //                                 <span class="product-label">Ukuran :</span> 
    //                                 <span class="product-value">${item.ukuran}</span>
    //                             </div>
    //                             ` : ''}
    //                             ${item.kemasan ? `
    //                             <div class="product-info-line">
    //                                 <span class="product-label">Kemasan :</span> 
    //                                 <span class="product-value">${item.kemasan}</span>
    //                             </div>
    //                             ` : ''}
    //                         </td>
    //                         <td>
    //                             <div class="stock-info-line">
    //                                 <span class="product-label">Stok saat ini:</span> 
    //                                 <span class="product-value">${item.stock_gudang || 0}</span>
    //                             </div>
    //                             <div class="stock-info-line">
    //                                 <span class="product-label">Jumlah Produksi :</span> 
    //                                 <span class="product-value">${item.jumlah_stock || 0}</span>
    //                             </div>
    //                             <div class="stock-info-line">
    //                                 <span class="product-label">Jumlah Pengalihan :</span> 
    //                                 <span class="product-value">${item.jumlah_keluar || 0}</span>
    //                             </div>
    //                             <div class="stock-info-line">
    //                                 <span class="product-label">Jumlah Retur :</span> 
    //                                 <span class="product-value">${item.jumlah_retur || 0}</span>
    //                             </div>
    //                         </td>
    //                     </tr>
    //                 `;
    //             });

    //             $tableBody.html(rowsHtml);
    //             $(`#total-items-${gudangId}`).text(`Total: ${response.recordsTotal} data`);
                
    //             // Mark as loaded
    //             loadedModals[gudangId] = true;

    //             // Setup search functionality
    //             $(`.search-detail-${gudangId}`).on('keyup', function() {
    //                 const searchText = $(this).val().toLowerCase();
    //                 $(`#${tableBodyId} tr`).each(function() {
    //                     const text = $(this).text().toLowerCase();
    //                     $(this).toggle(text.indexOf(searchText) > -1);
    //                 });
    //             });
    //         },
    //         error: function(xhr, error, code) {
    //             console.error('Ajax error:', error, code);
    //             $tableBody.html(`
    //                 <tr>
    //                     <td colspan="4" class="text-center py-5">
    //                         <div class="alert alert-danger m-0">
    //                             <i class="ri-error-warning-line me-2"></i>
    //                             Terjadi kesalahan saat memuat data. Silakan coba lagi.
    //                         </div>
    //                     </td>
    //                 </tr>
    //             `);
    //         }
    //     });
    // });

    // // Clean up when modal is hidden
    // $(document).on('hidden.bs.modal', '[id^="detailModal"]', function () {
    //     const modal = $(this);
    //     const gudangId = modal.attr('id').replace('detailModal', '');
        
    //     // Keep the loaded data for next time
    //     console.log('Modal closed:', gudangId);
    // });
});
</script>
@endpush