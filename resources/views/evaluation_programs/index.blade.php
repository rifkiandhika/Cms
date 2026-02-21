@extends('layouts.app')

@section('title', 'Program Evaluasi')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="ri-survey-line me-2"></i>Program Evaluasi Pelatihan</h2>
            <p class="text-muted mb-0">Kelola program evaluasi hasil pelatihan karyawan</p>
        </div>
        <a href="{{ route('evaluation-programs.create') }}" class="btn btn-primary">
            <i class="ri-add-circle-line me-1"></i>Tambah Program Evaluasi
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm rounded-circle bg-primary bg-opacity-10 me-3">
                            <i class="ri-survey-line text-primary fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small">Total Program</p>
                            <h4 class="mb-0">{{ $programs->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm rounded-circle bg-warning bg-opacity-10 me-3">
                            <i class="ri-draft-line text-warning fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small">Draft</p>
                            <h4 class="mb-0">{{ $programs->where('status', 'draft')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm rounded-circle bg-success bg-opacity-10 me-3">
                            <i class="ri-checkbox-circle-line text-success fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small">Aktif</p>
                            <h4 class="mb-0">{{ $programs->where('status', 'active')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm rounded-circle bg-info bg-opacity-10 me-3">
                            <i class="ri-user-line text-info fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small">Total Responses</p>
                            <h4 class="mb-0">{{ $programs->sum('responses_count') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="active">Aktif</option>
                        <option value="archived">Arsip</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="searchProgram" 
                           placeholder="Cari program evaluasi...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-secondary w-100" id="resetFilter">
                        <i class="ri-refresh-line me-1"></i>Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Programs Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0"><i class="ri-list-check me-2"></i>Data Program Evaluasi</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="30%">Judul Program</th>
                            <th width="20%">Materi Pelatihan</th>
                            <th width="12%">Tanggal</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="8%" class="text-center">Categories</th>
                            <th width="8%" class="text-center">Responses</th>
                            <th width="7%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programs as $index => $program)
                            <tr class="program-row" data-status="{{ $program->status }}">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs rounded bg-success bg-opacity-10 me-2 d-flex align-items-center justify-content-center">
                                            <i class="ri-survey-line text-success"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $program->title }}</strong>
                                            <br><small class="text-muted">{{ $program->program_number }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $program->materi_pelatihan }}</td>
                                <td>
                                    @if($program->hari_tanggal)
                                        <small>{{ $program->hari_tanggal->format('d M Y') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($program->status === 'draft')
                                        <span class="badge bg-warning">Draft</span>
                                    @elseif($program->status === 'active')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Arsip</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">
                                        {{ $program->categories_count }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">
                                        {{ $program->responses_count }} Peserta
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('evaluation-programs.show', $program->id) }}" 
                                           class="btn btn-sm btn-light"
                                           title="Lihat">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split" 
                                                data-bs-toggle="dropdown">
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('evaluation-programs.show', $program->id) }}">
                                                    <i class="ri-eye-line me-2"></i>Lihat Detail
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('evaluation-programs.edit', $program->id) }}">
                                                    <i class="ri-edit-line me-2"></i>Edit
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('evaluation-programs.destroy', $program->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Yakin ingin menghapus program ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="ri-delete-bin-line me-2"></i>Hapus
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="ri-survey-line text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-3">Belum ada program evaluasi</p>
                                    <a href="{{ route('evaluation-programs.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ri-add-line me-1"></i>Tambah Program Pertama
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

@push('scripts')
<script>
$(document).ready(function() {
    // Search
    $('#searchProgram').on('keyup', function() {
        const searchValue = $(this).val().toLowerCase();
        $('.program-row').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(searchValue));
        });
    });

    // Filter Status
    $('#filterStatus').on('change', function() {
        const status = $(this).val();
        if (status === '') {
            $('.program-row').show();
        } else {
            $('.program-row').each(function() {
                $(this).toggle($(this).data('status') === status);
            });
        }
    });

    // Reset
    $('#resetFilter').on('click', function() {
        $('#searchProgram').val('');
        $('#filterStatus').val('');
        $('.program-row').show();
    });

    // Auto dismiss alerts
    setTimeout(() => $('.alert').fadeOut(), 5000);
});
</script>
@endpush

@push('styles')
<style>
.avatar-xs { width: 2rem; height: 2rem; }
.avatar-sm { width: 3rem; height: 3rem; }
.table-hover tbody tr:hover { background-color: #f8f9fa; }
</style>
@endpush
@endsection