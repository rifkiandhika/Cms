@extends('layouts.app')

@section('title', 'Audit')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Audit</li>
@endsection

@section('content')
<div class="app-body">
    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-file-list-3-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Audit</p>
                        <h4 class="mb-0">{{ $audits->total() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-secondary bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-draft-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Draft</p>
                        <h4 class="mb-0">{{ $audits->where('status', 'draft')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-warning bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-time-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Sedang Berjalan</p>
                        <h4 class="mb-0">{{ $audits->where('status', 'in_progress')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-success bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-check-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Selesai</p>
                        <h4 class="mb-0">{{ $audits->where('status', 'completed')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="ri-file-list-line me-2"></i>Daftar Audit</h5>
            <a href="{{ route('audits.create') }}" class="btn btn-primary btn-sm">
                <i class="ri-add-circle-line me-1"></i>Buat Audit Baru
            </a>
        </div>

        <div class="card-body border-bottom bg-light">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Filter Status</label>
                    <select class="form-select form-select-sm" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="Draft">Draft</option>
                        <option value="Sedang Berjalan">Sedang Berjalan</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-bold">Cari</label>
                    <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari judul atau auditor...">
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                @if($audits->count() > 0)
                    <table class="table table-hover table-striped align-middle" id="auditTable">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Judul Audit</th>
                                <th width="120">Tanggal</th>
                                <th width="150">Auditor</th>
                                <th width="120" class="text-center">Status</th>
                                <th width="200" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audits as $audit)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + ($audits->currentPage() - 1) * $audits->perPage() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    {{ substr($audit->title, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <strong>{{ $audit->title }}</strong>
                                                @if($audit->notes)
                                                    <br><small class="text-muted">{{ Str::limit($audit->notes, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $audit->audit_date->format('d/m/Y') }}</td>
                                    <td>{{ $audit->auditor_name ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($audit->status == 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($audit->status == 'in_progress')
                                            <span class="badge bg-warning">Sedang Berjalan</span>
                                        @else
                                            <span class="badge bg-success">Selesai</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                <i class="ri-more-2-fill"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end shadow">

                                                <li>
                                                    <a href="{{ route('audits.show', $audit) }}" 
                                                    class="dropdown-item">
                                                        <i class="ri-edit-line me-2"></i>Isi Audit
                                                    </a>
                                                </li>

                                                @if($audit->status == 'completed')
                                                    <li>
                                                        <a href="{{ route('audits.report', $audit) }}" 
                                                        class="dropdown-item">
                                                            <i class="ri-file-text-line me-2"></i>Lihat Laporan
                                                        </a>
                                                    </li>
                                                @endif

                                                <li><hr class="dropdown-divider"></li>

                                                <li>
                                                    <form action="{{ route('audits.destroy', $audit) }}" 
                                                        method="POST" 
                                                        class="delete-confirm">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="dropdown-item text-danger">
                                                            <i class="ri-delete-bin-6-line me-2"></i>Hapus
                                                        </button>
                                                    </form>
                                                </li>

                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5">
                        <i class="ri-inbox-line ri-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-2">Belum ada data audit</p>
                        <a href="{{ route('audits.create') }}" class="btn btn-primary btn-sm">
                            <i class="ri-add-circle-line me-1"></i>Buat Audit Baru
                        </a>
                    </div>
                @endif
            </div>

            @if($audits->hasPages())
                <div class="mt-3">
                    {{ $audits->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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

    .table td {
        vertical-align: middle;
    }
    
    .table {
        border: 1px solid #ced4da !important;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    let table = $('#auditTable').DataTable({
        dom: 'rtip',
        pageLength: 10,
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
        order: [[2, 'desc']] // Sort by date descending
    });

    // Custom search
    $('#searchBox').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Filter status
    $('#filterStatus').on('change', function() {
        table.column(4).search(this.value).draw();
    });

    // SweetAlert delete confirm
    $(document).on('submit', '.delete-confirm', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data audit beserta semua jawaban akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });

    // Auto-dismiss alert
    setTimeout(() => $('.alert').fadeOut('slow'), 4000);
});
</script>
@endpush