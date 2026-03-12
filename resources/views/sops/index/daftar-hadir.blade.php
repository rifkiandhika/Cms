{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">
            <i class="ri-file-list-2-line me-2 text-success"></i>Formulir Daftar Hadir Pelatihan
        </h4>
        <p class="text-muted mb-0">Kelola form kehadiran peserta pelatihan</p>
    </div>
    <a href="{{ route('attendance-forms.create') }}" class="btn btn-success">
        <i class="ri-add-circle-line me-1"></i>Tambah Daftar Hadir
    </a>
</div>

{{-- Quick Stats --}}
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-soft-success me-3">
                        <span class="avatar-title bg-success text-white rounded-circle">
                            <i class="ri-file-list-2-line fs-5"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Total Formulir</p>
                        <h4 class="mb-0">{{ $attendanceForms->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-soft-info me-3">
                        <span class="avatar-title bg-info text-white rounded-circle">
                            <i class="ri-user-line fs-5"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Total Peserta</p>
                        <h4 class="mb-0">{{ $attendanceForms->sum('participants_count') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-soft-warning me-3">
                        <span class="avatar-title bg-warning text-white rounded-circle">
                            <i class="ri-calendar-line fs-5"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Bulan Ini</p>
                        <h4 class="mb-0">
                            {{ $attendanceForms->filter(fn($f) => $f->tanggal && \Carbon\Carbon::parse($f->tanggal)->isCurrentMonth())->count() }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Alert --}}
@if(session('dh_success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="ri-checkbox-circle-line me-2"></i>{{ session('dh_success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ri-list-check me-2"></i>Data Daftar Hadir</h5>
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text bg-white">
                    <i class="ri-search-line"></i>
                </span>
                <input type="text" id="searchAttendance" class="form-control"
                       placeholder="Cari formulir...">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-responsive mb-0">
            <thead class="table-light">
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="30%">Topik Pelatihan</th>
                    <th width="13%">Tanggal</th>
                    <th width="17%">Tempat</th>
                    <th width="17%">Instruktur</th>
                    <th width="8%" class="text-center">Peserta</th>
                    <th width="10%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendanceForms as $index => $form)
                    <tr class="attendance-row">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs rounded bg-success bg-opacity-10 me-2 d-flex align-items-center justify-content-center">
                                    <i class="ri-file-list-2-line text-success"></i>
                                </div>
                                <div>
                                    <strong>{{ $form->topik_pelatihan }}</strong>
                                    @if($form->catatan)
                                        <br><small class="text-muted">{{ Str::limit($form->catatan, 50) }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($form->tanggal)
                                <small>{{ \Carbon\Carbon::parse($form->tanggal)->format('d M Y') }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ Str::limit($form->tempat ?? '-', 30) }}</small>
                        </td>
                        <td>
                            <small>{{ Str::limit($form->instruktur ?? '-', 30) }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">
                                <i class="ri-user-line me-1"></i>{{ $form->participants_count ?? 0 }} Orang
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('attendance-forms.edit', $form->id) }}"
                                   class="btn btn-sm btn-light" title="Edit">
                                    <i class="ri-edit-line"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown">
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('attendance-forms.show', $form->id) }}">
                                            <i class="ri-eye-line me-2"></i>Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('attendance-forms.edit', [$form->id, 'sop_id' => $form->sop_id]) }}">
                                            <i class="ri-edit-line me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('attendance-forms.export-pdf', $form->id) }}"
                                           target="_blank" class="dropdown-item">
                                            <i class="ri-file-pdf-line text-danger me-2"></i>Export PDF
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('attendance-forms.destroy', $form->id) }}"
                                              method="POST" class="delete-hadir">
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
                        <td colspan="7" class="text-center py-5">
                            <i class="ri-file-list-2-line text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-3">Belum ada data daftar hadir pelatihan</p>
                            <a href="{{ route('attendance-forms.create') }}" class="btn btn-success btn-sm">
                                <i class="ri-add-line me-1"></i>Buat Daftar Hadir Pertama
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    $('#searchAttendance').on('keyup', function () {
        const val = $(this).val().toLowerCase();
        $('.attendance-row').each(function () {
            $(this).toggle($(this).text().toLowerCase().includes(val));
        });
    });
});

$(document).on('submit', '.delete-hadir', function (e) {
    e.preventDefault();
    var form = this;
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Daftar hadir akan dihapus permanen!",
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
</script>
@endpush