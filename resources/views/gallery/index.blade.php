@extends('layouts.app')

@section('title', 'Gallery')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Gallery</li>
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
        <div class="col-xl-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-gallery-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Gallery</p>
                        <h4 class="mb-0">{{ $galleries->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-success bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-image-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Gambar</p>
                        <h4 class="mb-0">{{ $galleries->sum('images_count') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="ri-gallery-line me-2"></i>Daftar Gallery</h5>
            <a href="{{ route('gallery.create') }}" class="btn btn-primary btn-sm">
                <i class="ri-add-circle-line me-1"></i>Tambah Gallery
            </a>
        </div>

        <div class="card-body border-bottom bg-light">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label small fw-bold">Cari</label>
                    <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari judul atau deskripsi...">
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                @if($galleries->count() > 0)
                    <table class="table table-hover table-striped align-middle" id="galleryTable">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th width="100" class="text-center">Jumlah Foto</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($galleries as $x => $data)
                                <tr>
                                    <td class="text-center">{{ $x + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    {{ substr($data->judul, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <strong>{{ $data->judul }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($data->deskripsi, 50) ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $data->images_count }} Foto</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="ri-more-2-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                <li>
                                                    <a href="{{ route('gallery.edit', $data->id) }}" class="dropdown-item">
                                                        <i class="ri-pencil-line me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('gallery.destroy', $data->id) }}" method="POST" class="delete-confirm">
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
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5">
                        <i class="ri-gallery-line ri-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-2">Belum ada data gallery</p>
                        <a href="{{ route('gallery.create') }}" class="btn btn-primary btn-sm">
                            <i class="ri-add-circle-line me-1"></i>Tambah Gallery
                        </a>
                    </div>
                @endif
            </div>
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

    .dropdown-item i {
        width: 20px;
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
    let table = $('#galleryTable').DataTable({
        dom: 'rtip',
        pageLength: 10,
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }
    });

    $('#searchBox').on('keyup', function() {
        table.search(this.value).draw();
    });

    $(document).on('submit', '.delete-confirm', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Gallery dan semua gambar akan dihapus permanen!",
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

    setTimeout(() => $('.alert').fadeOut('slow'), 4000);
});
</script>
@endpush