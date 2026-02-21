@extends('layouts.app')
@section('title', 'Pengendalian Hama')
@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Pengendalian Hama</li>
@endsection
@section('content')
<div class="app-body">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-bug-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Laporan</p>
                        <h4 class="mb-0">{{ $data->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-success bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-list-check-2 fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Catatan Harian</p>
                        <h4 class="mb-0">{{ $data->sum('details_count') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-warning bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-image-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Foto</p>
                        <h4 class="mb-0">{{ $data->sum(fn($d) => $d->gambar->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="ri-database-2-line me-2"></i>Daftar Pengendalian Hama</h5>
            <a href="{{ route('pengendalian-hama.create') }}" class="btn btn-primary btn-sm">
                <i class="ri-add-circle-line me-1"></i>Tambah Laporan
            </a>
        </div>
        <div class="card-body border-bottom bg-light">
            <label class="form-label small fw-bold">Cari</label>
            <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari lokasi, bulan, tahun...">
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if($data->count() > 0)
                    <table class="table table-hover table-striped align-middle" id="hamaTable">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Lokasi</th>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th class="text-center">Catatan</th>
                                <th class="text-center">Foto</th>
                                <th>Penanggung Jawab</th>
                                <th width="130" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $x => $item)
                            <tr>
                                <td class="text-center">{{ $x + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-2">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                <i class="ri-map-pin-line" style="font-size:0.8rem;"></i>
                                            </span>
                                        </div>
                                        <strong>{{ $item->lokasi }}</strong>
                                    </div>
                                </td>
                                <td>{{ $item->bulan }}</td>
                                <td>{{ $item->tahun }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $item->details_count }} catatan</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $item->gambar->count() > 0 ? 'bg-success' : 'bg-secondary' }}">
                                        <i class="ri-image-line me-1"></i>{{ $item->gambar->count() }}
                                    </span>
                                </td>
                                <td>{{ $item->penanggung_jawab ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="ri-more-2-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow">
                                            <li>
                                                <a href="{{ route('pengendalian-hama.show', $item->id) }}" class="dropdown-item">
                                                    <i class="ri-eye-line me-2"></i>Lihat Detail
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('pengendalian-hama.edit', $item->id) }}" class="dropdown-item">
                                                    <i class="ri-pencil-line me-2"></i>Edit
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('pengendalian-hama.destroy', $item->id) }}" method="POST" class="delete-confirm">
                                                    @csrf @method('DELETE')
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
                        <i class="ri-bug-line ri-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-2">Belum ada data pengendalian hama</p>
                        <a href="{{ route('pengendalian-hama.create') }}" class="btn btn-primary btn-sm">
                            <i class="ri-add-circle-line me-1"></i>Tambah Laporan
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
    .badge { font-weight: 500; padding: 0.4em 0.8em; font-size: 0.85rem; }
    .avatar-xs { height: 2rem; width: 2rem; }
    .avatar-sm { width: 3rem; height: 3rem; display: flex; align-items: center; justify-content: center; }
    .avatar-title { align-items: center; display: flex; font-weight: 600; height: 100%; justify-content: center; width: 100%; }
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.15) !important; }
    .table-hover tbody tr:hover { background-color: #f8f9fa; cursor: pointer; }
    .card { border-radius: 0.5rem; }
    .dropdown-item i { width: 20px; }
    .table td { vertical-align: middle; }
    .table { border: 1px solid #ced4da !important; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {
    let table = $('#hamaTable').DataTable({
        dom: 'rtip', pageLength: 10, responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }
    });
    $('#searchBox').on('keyup', function () { table.search(this.value).draw(); });
    $(document).on('submit', '.delete-confirm', function (e) {
        e.preventDefault(); var form = this;
        Swal.fire({ title: 'Hapus Laporan?', text: 'Semua catatan harian dan foto akan ikut terhapus!',
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
        }).then(r => { if (r.isConfirmed) form.submit(); });
    });
    setTimeout(() => $('.alert').fadeOut('slow'), 4000);
});
</script>
@endpush