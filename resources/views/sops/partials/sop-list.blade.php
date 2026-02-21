{{-- Statistik --}}
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="avatar-sm rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center me-3">
                    <i class="ri-file-list-3-line fs-4 text-white"></i>
                </div>
                <div>
                    <p class="text-muted mb-1">Total SOP</p>
                    <h4 class="mb-0">{{ $sops->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="avatar-sm rounded-circle bg-warning bg-gradient d-flex align-items-center justify-content-center me-3">
                    <i class="ri-draft-line fs-4 text-white"></i>
                </div>
                <div>
                    <p class="text-muted mb-1">Draft</p>
                    <h4 class="mb-0">{{ $sops->where('status', 'draft')->count() }}</h4>
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
                    <p class="text-muted mb-1">Aktif</p>
                    <h4 class="mb-0">{{ $sops->where('status', 'active')->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="avatar-sm rounded-circle bg-secondary bg-gradient d-flex align-items-center justify-content-center me-3">
                    <i class="ri-archive-line fs-4 text-white"></i>
                </div>
                <div>
                    <p class="text-muted mb-1">Arsip</p>
                    <h4 class="mb-0">{{ $sops->where('status', 'archived')->count() }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Table Section --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0"><i class="ri-database-2-line me-2"></i>Data SOP</h6>
        <a href="{{ route('sops.create') }}" class="btn btn-primary btn-sm">
            <i class="ri-add-circle-line me-1"></i>Tambah SOP
        </a>
    </div>

    <div class="card-body border-bottom bg-light">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Filter Status</label>
                <select class="form-select form-select-sm" id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="active">Aktif</option>
                    <option value="archived">Arsip</option>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label small fw-bold">Cari</label>
                <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari nama SOP atau nomor SOP...">
            </div>
        </div>
    </div>

    <div class="card-body">
        <div>
            @if($sops->count() > 0)
                <table class="table table-hover table-responsive align-middle" id="sopTable">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Nama SOP</th>
                            <th>No. SOP</th>
                            <th>Tanggal Efektif</th>
                            <th>Revisi</th>
                            <th width="100">Status</th>
                            <th width="100">Sections</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sops as $x => $data)
                            <tr>
                                <td class="text-center">{{ $x + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-2">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                {{ substr($data->nama_sop, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>{{ $data->nama_sop }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $data->no_sop }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($data->tanggal_efektif)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge bg-info">Rev. {{ $data->revisi }}</span>
                                </td>
                                <td>
                                    @if($data->status === 'draft')
                                        <span class="badge bg-warning">Draft</span>
                                    @elseif($data->status === 'active')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Arsip</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-purple">{{ $data->sections_count }} Section</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('sops.edit', $data->id) }}" 
                                        class="btn btn-sm btn-light"
                                        title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split" 
                                                data-bs-toggle="dropdown">
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="{{ route('sops.show', $data->id) }}" class="dropdown-item">
                                                    <i class="ri-eye-line me-2"></i>Lihat
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('sops.destroy', $data->id) }}" method="POST" class="delete-confirm">
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
                    <i class="ri-file-list-3-line ri-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-2">Belum ada data SOP</p>
                    <a href="{{ route('sops.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-circle-line me-1"></i>Tambah SOP
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    let table = $('#sopTable').DataTable({
        dom: 'rtip',
        pageLength: 10,
        responsive: true,
        order: [[3, 'desc']]
    });

    // Custom search
    $('#searchBox').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Filter status
    $('#filterStatus').on('change', function() {
        table.column(5).search(this.value).draw();
    });

    // SweetAlert delete confirm
    $(document).on('submit', '.delete-confirm', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data SOP akan dihapus permanen!",
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