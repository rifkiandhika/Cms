@extends('layouts.app')

@section('title', 'Detail Catatan Suhu - ' . $kontrolGudang->periode)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('sops.index') }}">SOP & Jadwal</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">{{ $kontrolGudang->periode }}</li>
@endsection

@section('content')
<div class="app-body">

    {{-- Alert --}}
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

    {{-- Info Header --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-calendar-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Periode</p>
                        <h5 class="mb-0">{{ $kontrolGudang->periode }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-success bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-list-check-2 fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Catatan</p>
                        <h4 class="mb-0">{{ $catatanSuhu->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-info bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-temp-hot-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Rata-rata Suhu Ruangan</p>
                        <h4 class="mb-0">
                            {{ $catatanSuhu->count() > 0 ? number_format($catatanSuhu->avg('suhu_ruangan'), 1) . '°C' : '-' }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-warning bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-drop-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Rata-rata Kelembapan</p>
                        <h4 class="mb-0">
                            {{ $catatanSuhu->count() > 0 ? number_format($catatanSuhu->avg('kelembapan'), 1) . '%' : '-' }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Catatan Harian --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h5 class="mb-0">
                    <i class="ri-database-2-line me-2"></i>Catatan Suhu Harian
                </h5>
                <small class="text-muted">{{ $kontrolGudang->nama_gudang }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('catatan-suhu.edit-periode', $kontrolGudang->id) }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="ri-pencil-line me-1"></i>Edit Periode
                </a>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahCatatan">
                    <i class="ri-add-circle-line me-1"></i>Tambah Catatan
                </button>
            </div>
        </div>

        <div class="card-body border-bottom bg-light">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label small fw-bold">Cari</label>
                    <input type="text" id="searchCatatan" class="form-control form-control-sm"
                           placeholder="Cari tanggal...">
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                @if($catatanSuhu->count() > 0)
                    <table class="table table-hover table-striped align-middle" id="catatanTable">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Tanggal</th>
                                <th class="text-center">Kebersihan</th>
                                <th class="text-center">Suhu Refrigerator</th>
                                <th class="text-center">Suhu Ruangan</th>
                                <th class="text-center">Kelembapan</th>
                                <th class="text-center">Keamanan</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($catatanSuhu->sortBy('tanggal') as $i => $catatan)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>
                                        <i class="ri-calendar-line me-1 text-primary"></i>
                                        {{ \Carbon\Carbon::parse($catatan->tanggal)->translatedFormat('d F Y') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $catatan->kebersihan ? 'bg-success' : 'bg-danger' }}">
                                            <i class="ri-{{ $catatan->kebersihan ? 'check' : 'close' }}-line"></i>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-primary text-primary border border-primary">
                                            {{ $catatan->suhu_refrigerator }}°C
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-warning text-warning border border-warning">
                                            {{ $catatan->suhu_ruangan }}°C
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-info text-info border border-info">
                                            {{ $catatan->kelembapan }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $catatan->keamanan ? 'bg-success' : 'bg-danger' }}">
                                            <i class="ri-{{ $catatan->keamanan ? 'check' : 'close' }}-line"></i>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                    data-bs-toggle="dropdown">
                                                <i class="ri-more-2-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                <li>
                                                    <button class="dropdown-item btn-edit-catatan"
                                                            data-id="{{ $catatan->id }}"
                                                            data-tanggal="{{ $catatan->tanggal?->format('Y-m-d') }}"
                                                            data-suhu_refrigerator="{{ $catatan->suhu_refrigerator }}"
                                                            data-suhu_ruangan="{{ $catatan->suhu_ruangan }}"
                                                            data-kelembapan="{{ $catatan->kelembapan }}"
                                                            data-kebersihan="{{ $catatan->kebersihan ? 1 : 0 }}"
                                                            data-keamanan="{{ $catatan->keamanan ? 1 : 0 }}">
                                                        <i class="ri-pencil-line me-2"></i>Edit
                                                    </button>
                                                </li>
                                                <li>
                                                    <form action="{{ route('catatan-suhu.destroy', $catatan->id) }}"
                                                          method="POST" class="delete-catatan-confirm">
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
                        <i class="ri-inbox-line ri-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-2">Belum ada catatan suhu untuk periode ini</p>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalTambahCatatan">
                            <i class="ri-add-circle-line me-1"></i>Tambah Catatan
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <div class="card-footer bg-white">
            <a href="{{ route('sops.index') }}" class="btn btn-secondary btn-sm">
                <i class="ri-arrow-left-line me-1"></i>Kembali ke SOP & Jadwal
            </a>
        </div>
    </div>
</div>

{{-- Modal Tambah Catatan --}}
<div class="modal fade" id="modalTambahCatatan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-add-circle-line me-2"></i>Tambah Catatan Suhu
                    <span class="badge bg-primary ms-2">{{ $kontrolGudang->periode }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('catatan-suhu.store') }}" method="POST">
                @csrf
                <input type="hidden" name="kontrol_gudang_id" value="{{ $kontrolGudang->id }}">
                <div class="modal-body">
                    @include('catatan-suhu.form-catatan')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="ri-save-line me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Catatan (1 modal, diisi via JS) --}}
<div class="modal fade" id="modalEditCatatan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-pencil-line me-2"></i>Edit Catatan Suhu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditCatatan" action="" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    @include('catatan-suhu.form-catatan', ['isEdit' => true])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="ri-save-line me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
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
    .bg-soft-warning  { background-color: rgba(255, 193, 7, 0.15) !important; }
    .bg-soft-info     { background-color: rgba(13, 202, 240, 0.15) !important; }
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
    // DataTable
    let table = $('#catatanTable').DataTable({
        dom: 'rtip',
        pageLength: 15,
        responsive: true,
        order: [[1, 'asc']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }
    });

    $('#searchCatatan').on('keyup', function () {
        table.search(this.value).draw();
    });

    // Isi modal edit via data-attribute
    $(document).on('click', '.btn-edit-catatan', function () {
        const id                = $(this).data('id');
        const tanggal           = $(this).data('tanggal');
        const suhu_refrigerator = $(this).data('suhu_refrigerator');
        const suhu_ruangan      = $(this).data('suhu_ruangan');
        const kelembapan        = $(this).data('kelembapan');
        const kebersihan        = $(this).data('kebersihan');
        const keamanan          = $(this).data('keamanan');

        $('#formEditCatatan').attr('action', '/catatan-suhu/' + id);
        $('#formEditCatatan [name="tanggal"]').val(tanggal);
        $('#formEditCatatan [name="suhu_refrigerator"]').val(suhu_refrigerator);
        $('#formEditCatatan [name="suhu_ruangan"]').val(suhu_ruangan);
        $('#formEditCatatan [name="kelembapan"]').val(kelembapan);
        $('#formEditCatatan [name="kebersihan"]').val(kebersihan);
        $('#formEditCatatan [name="keamanan"]').val(keamanan);

        $('#modalEditCatatan').modal('show');
    });

    // SweetAlert hapus catatan
    $(document).on('submit', '.delete-catatan-confirm', function (e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Hapus Catatan?',
            text: 'Data catatan suhu ini akan dihapus!',
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