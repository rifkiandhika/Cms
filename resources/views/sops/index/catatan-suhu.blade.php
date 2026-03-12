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

{{-- Statistik --}}
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="avatar-sm rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center me-3">
                    <i class="ri-store-2-line fs-4 text-white"></i>
                </div>
                <div>
                    <p class="text-muted mb-1">Total Periode</p>
                    <h4 class="mb-0">{{ $kontrolGudang->count() }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="avatar-sm rounded-circle bg-success bg-gradient d-flex align-items-center justify-content-center me-3">
                    <i class="ri-temp-hot-line fs-4 text-white"></i>
                </div>
                <div>
                    <p class="text-muted mb-1">Total Catatan</p>
                    <h4 class="mb-0">{{ $kontrolGudang->sum(fn($k) => $k->catatanSuhu->count()) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="avatar-sm rounded-circle bg-info bg-gradient d-flex align-items-center justify-content-center me-3">
                    <i class="ri-calendar-check-line fs-4 text-white"></i>
                </div>
                <div>
                    <p class="text-muted mb-1">Catatan Bulan Ini</p>
                    <h4 class="mb-0">
                        {{ $kontrolGudang->flatMap->catatanSuhu->filter(fn($c) => \Carbon\Carbon::parse($c->tanggal)->isCurrentMonth())->count() }}
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Daftar Periode --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0"><i class="ri-database-2-line me-2"></i>Daftar Periode Gudang</h5>
        <a href="{{ route('catatan-suhu.create' ) }}" class="btn btn-primary btn-sm">
            <i class="ri-add-circle-line me-1"></i>Tambah Periode
        </a>
    </div>

    <div class="card-body border-bottom bg-light">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label small fw-bold">Cari</label>
                <input type="text" id="searchSuhu" class="form-control form-control-sm"
                       placeholder="Cari periode atau nama gudang...">
            </div>
        </div>
    </div>

    <div class="card-body">
        <div>
            @if($kontrolGudang->count() > 0)
                <table class="table table-hover table-responsive align-middle" id="suhuPeriodeTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Periode</th>
                            <th>Nama Gudang</th>
                            <th class="text-center">Jumlah Catatan</th>
                            <th class="text-center">Terakhir Diisi</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kontrolGudang as $x => $kg)
                            <tr>
                                <td>{{ $x + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-2">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                <i class="ri-calendar-line" style="font-size:0.8rem;"></i>
                                            </span>
                                        </div>
                                        <strong>{{ $kg->periode }}</strong>
                                    </div>
                                </td>
                                <td>{{ $kg->nama_gudang }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">
                                        {{ $kg->catatanSuhu->count() }} catatan
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($kg->catatanSuhu->count() > 0)
                                        <span class="text-muted small">
                                            <i class="ri-time-line me-1"></i>
                                            {{ \Carbon\Carbon::parse($kg->catatanSuhu->sortByDesc('tanggal')->first()->tanggal)->translatedFormat('d F Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('catatan-suhu.edit-periode', $kg->id) }}" 
                                            class="btn btn-sm btn-light"
                                            title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split" 
                                                data-bs-toggle="dropdown">
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="{{ route('catatan-suhu.show', $kg->id) }}" class="dropdown-item">
                                                    <i class="ri-eye-line me-2"></i>Lihat Detail
                                                </a>
                                            </li>
                                            {{-- <li>
                                                <a href="{{ route('catatan-suhu.edit-periode', $kg->id) }}" class="dropdown-item">
                                                    <i class="ri-pencil-line me-2"></i>Edit
                                                </a>
                                            </li> --}}
                                            <li>
                                                <a href="{{ route('catatan-suhu.export-pdf', $kg->id) }}" 
                                                class="dropdown-item" 
                                                target="_blank">
                                                    <i class="ri ri-file-pdf-line text-danger"></i> Export PDF
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('catatan-suhu.periode.destroy', $kg->id) }}"
                                                        method="POST" class="delete-periode-confirm">
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
                    <i class="ri-store-2-line ri-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-2">Belum ada data periode gudang</p>
                    <a href="{{ route('catatan-suhu.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-circle-line me-1"></i>Tambah Periode
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    let table = $('#suhuPeriodeTable').DataTable({
        dom: 'rtip',
        pageLength: 10,
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }
    });

    $('#searchSuhu').on('keyup', function () {
        table.search(this.value).draw();
    });

    $(document).on('submit', '.delete-periode-confirm', function (e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Hapus Periode?',
            text: 'Semua catatan suhu dalam periode ini akan ikut terhapus!',
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
});
</script>
@endpush