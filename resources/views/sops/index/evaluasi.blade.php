{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
<div>
    <h4 class="mb-1"><i class="ri-survey-line me-2 text-primary"></i>Program Evaluasi Pelatihan</h4>
    <p class="text-muted mb-0">Kelola evaluasi efektivitas pelatihan karyawan</p>
</div>
<a href="{{ route('evaluation-programs.create' ) }}" class="btn btn-primary">
    <i class="ri-add-circle-line me-1"></i>Tambah Program Evaluasi
</a>
</div>

{{-- Quick Stats --}}
<div class="row mb-4">
<div class="col-md-3">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="avatar-sm rounded-circle bg-soft-primary me-3">
                    <span class="avatar-title bg-primary text-white rounded-circle">
                        <i class="ri-survey-line fs-5"></i>
                    </span>
                </div>
                <div>
                    <p class="text-muted mb-1 small">Total Program</p>
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
                <div class="avatar-sm rounded-circle bg-soft-warning me-3">
                    <span class="avatar-title bg-warning text-white rounded-circle">
                        <i class="ri-draft-line fs-5"></i>
                    </span>
                </div>
                <div>
                    <p class="text-muted mb-1 small">Draft</p>
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
                <div class="avatar-sm rounded-circle bg-soft-success me-3">
                    <span class="avatar-title bg-success text-white rounded-circle">
                        <i class="ri-checkbox-circle-line fs-5"></i>
                    </span>
                </div>
                <div>
                    <p class="text-muted mb-1 small">Aktif</p>
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
                <div class="avatar-sm rounded-circle bg-soft-secondary me-3">
                    <span class="avatar-title bg-secondary text-white rounded-circle">
                        <i class="ri-archive-line fs-5"></i>
                    </span>
                </div>
                <div>
                    <p class="text-muted mb-1 small">Arsip</p>
                    <h4 class="mb-0">{{ $programs->where('status', 'archived')->count() }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
<div class="card-header bg-white border-bottom">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="ri-list-check me-2"></i>Data Program Evaluasi</h5>
        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-white">
                <i class="ri-search-line"></i>
            </span>
            <input type="text" id="searchProgram" class="form-control" placeholder="Cari program...">
        </div>
    </div>
</div>
<div class="card-body p-0">
    <div>
        <table class="table table-hover table-responsive mb-0">
            <thead class="table-light">
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="30%">Nama Program</th>
                    <th width="15%">No. Program</th>
                    <th width="12%">Tanggal</th>
                    <th width="13%">Tempat</th>
                    <th width="8%" class="text-center">Status</th>
                    <th width="8%" class="text-center">Peserta</th>
                    <th width="9%" class="text-center">Responses</th>
                    <th width="5%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($programs as $index => $program)
                    <tr class="program-row">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs rounded bg-primary bg-opacity-10 me-2 d-flex align-items-center justify-content-center">
                                    <i class="ri-survey-line text-primary"></i>
                                </div>
                                <div>
                                    <strong>{{ $program->title }}</strong>
                                    @if($program->description)
                                        <br><small class="text-muted">{{ Str::limit($program->description, 50) }}</small>
                                    @endif
                                    <br><small class="text-muted"><i class="ri-book-open-line me-1"></i>{{ Str::limit($program->materi_pelatihan, 40) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $program->program_number }}
                            </span>
                        </td>
                        <td>
                            @if($program->hari_tanggal)
                                <small>{{ $program->hari_tanggal->format('d M Y') }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($program->tempat_pelatihan)
                                <small>{{ Str::limit($program->tempat_pelatihan, 30) }}</small>
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
                            <span class="badge bg-info">
                                {{ $program->participants_count }} Orang
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-purple">
                                {{ $program->responses_count }} Respon
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('evaluation-programs.edit', $program->id) }}"
                                    class="btn btn-sm btn-light" title="Edit">
                                    <i class="ri-edit-line"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('evaluation-programs.show', $program->id) }}">
                                            <i class="ri-eye-line me-2"></i>Lihat Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('evaluation-programs.edit', [$program->id, 'sop_id' => $program->sop_id]) }}">
                                            <i class="ri-edit-line me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('evaluation-programs.generate-all-pdf', $program->id) }}"
                                            target="_blank">
                                            <i class="ri-file-pdf-line me-2 text-danger"></i>Export PDF
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('evaluation-programs.destroy', $program->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus program evaluasi ini? Semua data terkait akan ikut terhapus.')">
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
                        <td colspan="9" class="text-center py-5">
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

@push('scripts')
<script>
$(document).ready(function () {
// Search filter
$('#searchProgram').on('keyup', function () {
    const searchValue = $(this).val().toLowerCase();
    $('.program-row').each(function () {
        $(this).toggle($(this).text().toLowerCase().includes(searchValue));
    });
});

// Auto dismiss alerts
setTimeout(() => $('.alert').fadeOut(), 5000);
});
</script>
@endpush

@push('styles')
<style>
.bg-purple          { background-color: #6f42c1; color: white; }
.avatar-xs          { width: 2rem; height: 2rem; }
.avatar-sm          { width: 3rem; height: 3rem; }
.bg-soft-primary    { background-color: rgba(13, 110, 253, 0.15) !important; }
.bg-soft-success    { background-color: rgba(25, 135, 84, 0.15) !important; }
.bg-soft-warning    { background-color: rgba(255, 193, 7, 0.15) !important; }
.bg-soft-secondary  { background-color: rgba(108, 117, 125, 0.15) !important; }
.bg-soft-info       { background-color: rgba(13, 202, 240, 0.15) !important; }
.table-hover tbody tr:hover { background-color: #f8f9fa; }
</style>
@endpush