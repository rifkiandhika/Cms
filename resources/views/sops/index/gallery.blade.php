{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="ri-image-line me-2 text-primary"></i>Gallery</h4>
        <p class="text-muted mb-0">Kelola galeri dokumentasi pelatihan</p>
    </div>
    <a href="{{ route('gallery.create') }}" class="btn btn-primary">
        <i class="ri-add-circle-line me-1"></i>Tambah Galeri
    </a>
</div>

{{-- Quick Stats --}}
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-soft-primary me-3">
                        <span class="avatar-title bg-primary text-white rounded-circle">
                            <i class="ri-image-line fs-5"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Total Galeri</p>
                        <h4 class="mb-0">{{ $galleries->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-soft-success me-3">
                        <span class="avatar-title bg-success text-white rounded-circle">
                            <i class="ri-file-image-line fs-5"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Total Foto</p>
                        {{-- Ganti images_count dengan nama relasi yang sesuai --}}
                        <h4 class="mb-0">{{ $galleries->sum('images_count') }}</h4>
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
            <h5 class="mb-0"><i class="ri-list-check me-2"></i>Data Galeri</h5>
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text bg-white">
                    <i class="ri-search-line"></i>
                </span>
                <input type="text" id="searchGallery" class="form-control"
                       placeholder="Cari galeri...">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-responsive mb-0">
            <thead class="table-light">
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th width="10%" class="text-center">Foto</th>
                    <th width="8%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($galleries as $index => $item)
                    <tr class="gallery-row">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs rounded bg-primary bg-opacity-10 me-2 d-flex align-items-center justify-content-center">
                                    <i class="ri-image-line text-primary"></i>
                                </div>
                                <strong>{{ $item->judul }}</strong>
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">{{ Str::limit($item->deskripsi ?? '-', 60) }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">
                                <i class="ri-image-line me-1"></i>{{ $item->images_count ?? 0 }} Foto
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('gallery.edit', [$item->id, 'sop_id' => $item->sop_id]) }}"
                                   class="btn btn-sm btn-light" title="Edit">
                                    <i class="ri-edit-line"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown">
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a href="{{ route('gallery.export.pdf', $item->id) }}"
                                           class="dropdown-item" target="_blank">
                                            <i class="ri-file-pdf-line text-danger me-2"></i>Export PDF
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('gallery.destroy', $item->id) }}"
                                              method="POST" class="delete-gallery">
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
                        <td colspan="5" class="text-center py-5">
                            <i class="ri-image-line text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-3">Belum ada data galeri</p>
                            <a href="{{ route('gallery.create') }}" class="btn btn-primary btn-sm">
                                <i class="ri-add-line me-1"></i>Tambah Galeri Pertama
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
    $('#searchGallery').on('keyup', function () {
        const val = $(this).val().toLowerCase();
        $('.gallery-row').each(function () {
            $(this).toggle($(this).text().toLowerCase().includes(val));
        });
    });
});

$(document).on('submit', '.delete-gallery', function (e) {
    e.preventDefault();
    var form = this;
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Galeri akan dihapus permanen!",
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

@push('styles')
<style>
.avatar-xs { width: 2rem; height: 2rem; }
.avatar-sm { width: 3rem; height: 3rem; }
.bg-soft-primary  { background-color: rgba(13, 110, 253, 0.15) !important; }
.bg-soft-success  { background-color: rgba(25, 135, 84, 0.15) !important; }
.bg-soft-warning  { background-color: rgba(255, 193, 7, 0.15) !important; }
.table-hover tbody tr:hover { background-color: #f8f9fa; }
</style>
@endpush