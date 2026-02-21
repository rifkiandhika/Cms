
@extends('layouts.app')
@section('title', 'Tambah Pengendalian Hama')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pengendalian-hama.index') }}">Pengendalian Hama</a></li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Tambah</li>
@endsection
@section('content')
<div class="app-body">
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="ri-error-warning-line me-2"></i><strong>Terdapat kesalahan input:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('pengendalian-hama.store') }}" method="POST" enctype="multipart/form-data" id="formHama">
        @csrf

        {{-- HEADER --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="ri-information-line me-2"></i>Informasi Laporan</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi" class="form-control"
                               placeholder="e.g. Gudang Penyimpanan PT. Premiere Alkes Nusindo"
                               value="{{ old('lokasi') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Bulan <span class="text-danger">*</span></label>
                        <select name="bulan" class="form-select" required>
                            <option value="">-- Pilih Bulan --</option>
                            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                                <option value="{{ $b }}" {{ old('bulan') == $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Tahun <span class="text-danger">*</span></label>
                        <input type="number" name="tahun" class="form-control"
                               placeholder="e.g. 2023" min="2000" max="2099"
                               value="{{ old('tahun', date('Y')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Penanggung Jawab Teknis</label>
                        <input type="text" name="penanggung_jawab" class="form-control"
                               placeholder="Nama penanggung jawab"
                               value="{{ old('penanggung_jawab') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL DETAIL HARIAN --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <div>
                    <h5 class="mb-0"><i class="ri-table-line me-2"></i>Catatan Pengendalian Hama</h5>
                    <small class="text-muted">Klik "+ Tambah Baris" untuk menambah catatan harian</small>
                </div>
                <button type="button" class="btn btn-success btn-sm" id="btnTambahBaris">
                    <i class="ri-add-line me-1"></i>Tambah Baris
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="tabelDetail">
                        <thead class="table-light text-center" style="font-size:0.82rem;">
                            <tr>
                                <th rowspan="2" class="align-middle" width="110">Tanggal</th>
                                <th rowspan="2" class="align-middle" width="80">Hari</th>
                                <th rowspan="2" class="align-middle" width="85">Waktu</th>
                                <th colspan="4" class="text-center">Treatment</th>
                                <th colspan="2" class="text-center">Perangkap</th>
                                <th rowspan="2" class="align-middle" width="90">Evaluasi</th>
                                <th colspan="2" class="text-center">Petugas</th>
                                <th rowspan="2" class="align-middle" width="100">Keterangan</th>
                                <th rowspan="2" class="align-middle" width="50">Aksi</th>
                            </tr>
                            <tr>
                                <th width="40">C</th>
                                <th width="40">B</th>
                                <th width="40">F</th>
                                <th width="40">I</th>
                                <th width="90">Perlakuan</th>
                                <th width="70">Jml Hama</th>
                                <th width="110">Nama</th>
                                <th width="60">Paraf</th>
                            </tr>
                        </thead>
                        <tbody id="bodyDetail">
                            {{-- Baris awal --}}
                            <tr class="baris-detail">
                                <td><input type="date" name="rows[0][tanggal]" class="form-control form-control-sm input-tanggal" required></td>
                                <td><input type="text" name="rows[0][hari]" class="form-control form-control-sm" readonly placeholder="Auto"></td>
                                <td><input type="time" name="rows[0][waktu]" class="form-control form-control-sm"></td>
                                <td class="text-center"><input type="checkbox" name="rows[0][treatment_c]" class="form-check-input" value="1"></td>
                                <td class="text-center"><input type="checkbox" name="rows[0][treatment_b]" class="form-check-input" value="1"></td>
                                <td class="text-center"><input type="checkbox" name="rows[0][treatment_f]" class="form-check-input" value="1"></td>
                                <td class="text-center"><input type="checkbox" name="rows[0][treatment_i]" class="form-check-input" value="1"></td>
                                <td>
                                    <select name="rows[0][perangkap_perlakuan]" class="form-select form-select-sm">
                                        <option value="">-</option>
                                        <option value="K">K</option>
                                        <option value="MS">MS</option>
                                        <option value="L">L</option>
                                        <option value="RB">RB</option>
                                    </select>
                                </td>
                                <td><input type="number" name="rows[0][jumlah_hama]" class="form-control form-control-sm" value="0" min="0"></td>
                                <td><input type="text" name="rows[0][evaluasi]" class="form-control form-control-sm" placeholder="e.g. none"></td>
                                <td><input type="text" name="rows[0][nama_petugas]" class="form-control form-control-sm" placeholder="Nama"></td>
                                <td class="text-center"><input type="checkbox" name="rows[0][paraf_petugas]" class="form-check-input" value="1"></td>
                                <td><input type="text" name="rows[0][keterangan]" class="form-control form-control-sm" placeholder="e.g. OK"></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris" title="Hapus baris">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Keterangan Treatment --}}
            <div class="card-footer bg-light">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted fw-bold">Keterangan Treatment:</small><br>
                        <small class="text-muted">C: CoolFog &nbsp;|&nbsp; B: Baiting &nbsp;|&nbsp; F: Fogging &nbsp;|&nbsp; I: Inspeksi</small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted fw-bold">Keterangan Perangkap:</small><br>
                        <small class="text-muted">K: Cockroach (Kecoa) &nbsp;|&nbsp; MS: Mosq Trap (Nyamuk) &nbsp;|&nbsp; L: Fly Trap (Lalat) &nbsp;|&nbsp; RB: Rat Box (Tikus)</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- UPLOAD GAMBAR --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="ri-image-line me-2"></i>Foto Pest Control</h5>
                <small class="text-muted">Format: JPG, JPEG, PNG, WEBP. Maksimal 2MB per foto.</small>
            </div>
            <div class="card-body">
                {{-- Drop Area --}}
                <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center mb-3" id="dropArea"
                     style="border-color: #ced4da !important; cursor: pointer; transition: all 0.2s;">
                    <i class="ri-upload-cloud-2-line ri-3x text-muted mb-2 d-block"></i>
                    <p class="text-muted mb-1">Drag & drop foto di sini, atau</p>
                    <label for="inputGambar" class="btn btn-outline-primary btn-sm">
                        <i class="ri-folder-open-line me-1"></i>Pilih Foto
                    </label>
                    <input type="file" name="gambar[]" id="inputGambar" class="d-none"
                           accept="image/*" multiple>
                    <p class="text-muted small mt-2 mb-0">Bisa pilih lebih dari 1 foto sekaligus</p>
                </div>

                {{-- Preview --}}
                <div class="row g-3" id="previewContainer"></div>
            </div>
        </div>

        {{-- ACTION BUTTON --}}
        <div class="d-flex justify-content-between mb-4">
            <a href="{{ route('pengendalian-hama.index') }}" class="btn btn-secondary btn-sm">
                <i class="ri-arrow-left-line me-1"></i>Kembali
            </a>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="ri-save-line me-1"></i>Simpan Laporan
            </button>
        </div>

    </form>
</div>
@endsection

@push('styles')
<style>
    .table th, .table td { vertical-align: middle; font-size: 0.83rem; }
    .form-control-sm, .form-select-sm { font-size: 0.82rem; }
    .upload-area:hover, .upload-area.drag-over { background-color: #f0f4ff !important; border-color: #0d6efd !important; }
    .preview-card { position: relative; }
    .preview-card .btn-remove { position: absolute; top: 4px; right: 4px; padding: 2px 6px; font-size: 0.75rem; }
    .preview-card img { width: 100%; height: 130px; object-fit: cover; border-radius: 6px; }
    .card { border-radius: 0.5rem; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {
    let rowIndex = 1;
    const hariId = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

    // Auto-isi hari dari tanggal
    $(document).on('change', '.input-tanggal', function () {
        const d = new Date($(this).val());
        if (!isNaN(d)) {
            $(this).closest('tr').find('input[name*="[hari]"]').val(hariId[d.getDay()]);
        }
    });

    // Tambah baris baru
    $('#btnTambahBaris').on('click', function () {
        const tpl = `
        <tr class="baris-detail">
            <td><input type="date" name="rows[${rowIndex}][tanggal]" class="form-control form-control-sm input-tanggal" required></td>
            <td><input type="text" name="rows[${rowIndex}][hari]" class="form-control form-control-sm" readonly placeholder="Auto"></td>
            <td><input type="time" name="rows[${rowIndex}][waktu]" class="form-control form-control-sm"></td>
            <td class="text-center"><input type="checkbox" name="rows[${rowIndex}][treatment_c]" class="form-check-input" value="1"></td>
            <td class="text-center"><input type="checkbox" name="rows[${rowIndex}][treatment_b]" class="form-check-input" value="1"></td>
            <td class="text-center"><input type="checkbox" name="rows[${rowIndex}][treatment_f]" class="form-check-input" value="1"></td>
            <td class="text-center"><input type="checkbox" name="rows[${rowIndex}][treatment_i]" class="form-check-input" value="1"></td>
            <td>
                <select name="rows[${rowIndex}][perangkap_perlakuan]" class="form-select form-select-sm">
                    <option value="">-</option>
                    <option value="K">K</option>
                    <option value="MS">MS</option>
                    <option value="L">L</option>
                    <option value="RB">RB</option>
                </select>
            </td>
            <td><input type="number" name="rows[${rowIndex}][jumlah_hama]" class="form-control form-control-sm" value="0" min="0"></td>
            <td><input type="text" name="rows[${rowIndex}][evaluasi]" class="form-control form-control-sm" placeholder="e.g. none"></td>
            <td><input type="text" name="rows[${rowIndex}][nama_petugas]" class="form-control form-control-sm" placeholder="Nama"></td>
            <td class="text-center"><input type="checkbox" name="rows[${rowIndex}][paraf_petugas]" class="form-check-input" value="1"></td>
            <td><input type="text" name="rows[${rowIndex}][keterangan]" class="form-control form-control-sm" placeholder="e.g. OK"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </td>
        </tr>`;
        $('#bodyDetail').append(tpl);
        rowIndex++;
    });

    // Hapus baris
    $(document).on('click', '.btn-hapus-baris', function () {
        if ($('.baris-detail').length > 1) {
            $(this).closest('tr').remove();
        } else {
            Swal.fire({ icon: 'warning', title: 'Minimal 1 baris!', text: 'Harus ada minimal 1 catatan harian.', timer: 2000, showConfirmButton: false });
        }
    });

    // ========================
    // UPLOAD PREVIEW
    // ========================
    let fileList = [];

    function renderPreview() {
        $('#previewContainer').empty();
        fileList.forEach((file, i) => {
            const url = URL.createObjectURL(file);
            $('#previewContainer').append(`
                <div class="col-6 col-md-3 col-lg-2 preview-card" data-index="${i}">
                    <div class="position-relative">
                        <img src="${url}" alt="${file.name}" class="rounded shadow-sm">
                        <button type="button" class="btn btn-danger btn-remove btn-remove-preview" data-index="${i}">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <p class="text-muted small mt-1 mb-0 text-truncate">${file.name}</p>
                </div>
            `);
        });
        syncFileInput();
    }

    function syncFileInput() {
        const dt = new DataTransfer();
        fileList.forEach(f => dt.items.add(f));
        document.getElementById('inputGambar').files = dt.files;
    }

    $('#inputGambar').on('change', function () {
        Array.from(this.files).forEach(f => fileList.push(f));
        renderPreview();
    });

    $(document).on('click', '.btn-remove-preview', function () {
        fileList.splice($(this).data('index'), 1);
        renderPreview();
    });

    // Drag & Drop
    const dropArea = document.getElementById('dropArea');
    dropArea.addEventListener('dragover', e => { e.preventDefault(); dropArea.classList.add('drag-over'); });
    dropArea.addEventListener('dragleave', () => dropArea.classList.remove('drag-over'));
    dropArea.addEventListener('drop', e => {
        e.preventDefault();
        dropArea.classList.remove('drag-over');
        Array.from(e.dataTransfer.files).forEach(f => {
            if (f.type.startsWith('image/')) fileList.push(f);
        });
        renderPreview();
    });
    dropArea.addEventListener('click', function(e) {
        if (!e.target.closest('label') && !e.target.closest('button')) {
            document.getElementById('inputGambar').click();
        }
    });
});
</script>
@endpush