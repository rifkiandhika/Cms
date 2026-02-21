
@extends('layouts.app')
@section('title', 'Edit Pengendalian Hama')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pengendalian-hama.index') }}">Pengendalian Hama</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pengendalian-hama.show', $pengendalianHama->id) }}">{{ $pengendalianHama->bulan }} {{ $pengendalianHama->tahun }}</a></li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Edit</li>
@endsection
@section('content')
<div class="app-body">
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="ri-error-warning-line me-2"></i><strong>Terdapat kesalahan input:</strong>
            <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('pengendalian-hama.update', $pengendalianHama->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

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
                               value="{{ old('lokasi', $pengendalianHama->lokasi) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Bulan <span class="text-danger">*</span></label>
                        <select name="bulan" class="form-select" required>
                            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                                <option value="{{ $b }}" {{ old('bulan', $pengendalianHama->bulan) == $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Tahun <span class="text-danger">*</span></label>
                        <input type="number" name="tahun" class="form-control"
                               value="{{ old('tahun', $pengendalianHama->tahun) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Penanggung Jawab Teknis</label>
                        <input type="text" name="penanggung_jawab" class="form-control"
                               value="{{ old('penanggung_jawab', $pengendalianHama->penanggung_jawab) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL DETAIL --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <div>
                    <h5 class="mb-0"><i class="ri-table-line me-2"></i>Catatan Pengendalian Hama</h5>
                    <small class="text-muted">Semua baris lama akan diganti dengan baris yang ada di sini</small>
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
                                <th colspan="4">Treatment</th>
                                <th colspan="2">Perangkap</th>
                                <th rowspan="2" class="align-middle" width="90">Evaluasi</th>
                                <th colspan="2">Petugas</th>
                                <th rowspan="2" class="align-middle" width="100">Keterangan</th>
                                <th rowspan="2" class="align-middle" width="50">Aksi</th>
                            </tr>
                            <tr>
                                <th width="40">C</th><th width="40">B</th><th width="40">F</th><th width="40">I</th>
                                <th width="90">Perlakuan</th><th width="70">Jml Hama</th>
                                <th width="110">Nama</th><th width="60">Paraf</th>
                            </tr>
                        </thead>
                        <tbody id="bodyDetail">
                            @foreach($pengendalianHama->details as $i => $d)
                            <tr class="baris-detail">
                                <td><input type="date" name="rows[{{ $i }}][tanggal]" class="form-control form-control-sm input-tanggal" value="{{ $d->tanggal?->format('Y-m-d') }}" required></td>
                                <td><input type="text" name="rows[{{ $i }}][hari]" class="form-control form-control-sm" readonly value="{{ $d->hari }}"></td>
                                <td><input type="time" name="rows[{{ $i }}][waktu]" class="form-control form-control-sm" value="{{ $d->waktu ? \Carbon\Carbon::parse($d->waktu)->format('H:i') : '' }}"></td>
                                <td class="text-center"><input type="checkbox" name="rows[{{ $i }}][treatment_c]" class="form-check-input" value="1" {{ $d->treatment_c ? 'checked' : '' }}></td>
                                <td class="text-center"><input type="checkbox" name="rows[{{ $i }}][treatment_b]" class="form-check-input" value="1" {{ $d->treatment_b ? 'checked' : '' }}></td>
                                <td class="text-center"><input type="checkbox" name="rows[{{ $i }}][treatment_f]" class="form-check-input" value="1" {{ $d->treatment_f ? 'checked' : '' }}></td>
                                <td class="text-center"><input type="checkbox" name="rows[{{ $i }}][treatment_i]" class="form-check-input" value="1" {{ $d->treatment_i ? 'checked' : '' }}></td>
                                <td>
                                    <select name="rows[{{ $i }}][perangkap_perlakuan]" class="form-select form-select-sm">
                                        <option value="">-</option>
                                        @foreach(['K','MS','L','RB'] as $p)
                                            <option value="{{ $p }}" {{ $d->perangkap_perlakuan == $p ? 'selected' : '' }}>{{ $p }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="rows[{{ $i }}][jumlah_hama]" class="form-control form-control-sm" value="{{ $d->jumlah_hama }}" min="0"></td>
                                <td><input type="text" name="rows[{{ $i }}][evaluasi]" class="form-control form-control-sm" value="{{ $d->evaluasi }}"></td>
                                <td><input type="text" name="rows[{{ $i }}][nama_petugas]" class="form-control form-control-sm" value="{{ $d->nama_petugas }}"></td>
                                <td class="text-center"><input type="checkbox" name="rows[{{ $i }}][paraf_petugas]" class="form-check-input" value="1" {{ $d->paraf_petugas ? 'checked' : '' }}></td>
                                <td><input type="text" name="rows[{{ $i }}][keterangan]" class="form-control form-control-sm" value="{{ $d->keterangan }}"></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris"><i class="ri-delete-bin-line"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light">
                <div class="row">
                    <div class="col-md-6"><small class="text-muted fw-bold">Treatment:</small> <small class="text-muted">C: CoolFog | B: Baiting | F: Fogging | I: Inspeksi</small></div>
                    <div class="col-md-6"><small class="text-muted fw-bold">Perangkap:</small> <small class="text-muted">K: Kecoa | MS: Nyamuk | L: Lalat | RB: Tikus</small></div>
                </div>
            </div>
        </div>

        {{-- UPLOAD GAMBAR BARU --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="ri-image-add-line me-2"></i>Tambah Foto Baru</h5>
                <small class="text-muted">Foto yang sudah ada tidak akan terhapus. Untuk hapus foto, gunakan tombol hapus di halaman detail.</small>
            </div>
            <div class="card-body">
                @if($pengendalianHama->gambar->count() > 0)
                    <p class="text-muted small mb-3"><i class="ri-image-line me-1"></i>Foto yang sudah ada: <strong>{{ $pengendalianHama->gambar->count() }} foto</strong></p>
                    <div class="row g-2 mb-3">
                        @foreach($pengendalianHama->gambar as $g)
                        <div class="col-4 col-md-2">
                            <img src="{{ Storage::url($g->path_gambar) }}" class="img-fluid rounded shadow-sm"
                                 style="width:100%; height:80px; object-fit:cover;" alt="{{ $g->nama_file }}">
                        </div>
                        @endforeach
                    </div>
                @endif

                <div class="upload-area  border-2 border-dashed rounded-3 p-4 text-center mb-3" id="dropArea"
                     style="border-color: #ced4da !important; cursor: pointer;">
                    <i class="ri-upload-cloud-2-line ri-3x text-muted mb-2 d-block"></i>
                    <p class="text-muted mb-1">Drag & drop foto di sini, atau</p>
                    <label for="inputGambar" class="btn btn-outline-primary btn-sm">
                        <i class="ri-folder-open-line me-1"></i>Pilih Foto Baru
                    </label>
                    <input type="file" name="gambar[]" id="inputGambar" class="d-none" accept="image/*" multiple>
                </div>
                <div class="row g-3" id="previewContainer"></div>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-4">
            <a href="{{ route('pengendalian-hama.show', $pengendalianHama->id) }}" class="btn btn-secondary btn-sm">
                <i class="ri-arrow-left-line me-1"></i>Kembali
            </a>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="ri-save-line me-1"></i>Simpan Perubahan
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
    .preview-card img { width: 100%; height: 130px; object-fit: cover; border-radius: 6px; }
    .card { border-radius: 0.5rem; }
</style>
@endpush
@push('scripts')
<script>
$(document).ready(function () {
    let rowIndex = {{ $pengendalianHama->details->count() }};
    const hariId = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

    $(document).on('change', '.input-tanggal', function () {
        const d = new Date($(this).val());
        if (!isNaN(d)) $(this).closest('tr').find('input[name*="[hari]"]').val(hariId[d.getDay()]);
    });

    $('#btnTambahBaris').on('click', function () {
        const tpl = `<tr class="baris-detail">
            <td><input type="date" name="rows[${rowIndex}][tanggal]" class="form-control form-control-sm input-tanggal" required></td>
            <td><input type="text" name="rows[${rowIndex}][hari]" class="form-control form-control-sm" readonly placeholder="Auto"></td>
            <td><input type="time" name="rows[${rowIndex}][waktu]" class="form-control form-control-sm"></td>
            <td class="text-center"><input type="checkbox" name="rows[${rowIndex}][treatment_c]" class="form-check-input" value="1"></td>
            <td class="text-center"><input type="checkbox" name="rows[${rowIndex}][treatment_b]" class="form-check-input" value="1"></td>
            <td class="text-center"><input type="checkbox" name="rows[${rowIndex}][treatment_f]" class="form-check-input" value="1"></td>
            <td class="text-center"><input type="checkbox" name="rows[${rowIndex}][treatment_i]" class="form-check-input" value="1"></td>
            <td><select name="rows[${rowIndex}][perangkap_perlakuan]" class="form-select form-select-sm">
                <option value="">-</option><option value="K">K</option><option value="MS">MS</option><option value="L">L</option><option value="RB">RB</option>
            </select></td>
            <td><input type="number" name="rows[${rowIndex}][jumlah_hama]" class="form-control form-control-sm" value="0" min="0"></td>
            <td><input type="text" name="rows[${rowIndex}][evaluasi]" class="form-control form-control-sm"></td>
            <td><input type="text" name="rows[${rowIndex}][nama_petugas]" class="form-control form-control-sm"></td>
            <td class="text-center"><input type="checkbox" name="rows[${rowIndex}][paraf_petugas]" class="form-check-input" value="1"></td>
            <td><input type="text" name="rows[${rowIndex}][keterangan]" class="form-control form-control-sm"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris"><i class="ri-delete-bin-line"></i></button></td>
        </tr>`;
        $('#bodyDetail').append(tpl);
        rowIndex++;
    });

    $(document).on('click', '.btn-hapus-baris', function () {
        if ($('.baris-detail').length > 1) $(this).closest('tr').remove();
        else Swal.fire({ icon: 'warning', title: 'Minimal 1 baris!', timer: 2000, showConfirmButton: false });
    });

    let fileList = [];
    function renderPreview() {
        $('#previewContainer').empty();
        fileList.forEach((file, i) => {
            const url = URL.createObjectURL(file);
            $('#previewContainer').append(`<div class="col-6 col-md-3 col-lg-2 preview-card">
                <div class="position-relative">
                    <img src="${url}" class="rounded shadow-sm">
                    <button type="button" class="btn btn-danger btn-remove btn-remove-preview position-absolute top-0 end-0 m-1" data-index="${i}" style="padding:2px 6px; font-size:0.75rem;"><i class="ri-close-line"></i></button>
                </div>
                <p class="text-muted small mt-1 mb-0 text-truncate">${file.name}</p>
            </div>`);
        });
        const dt = new DataTransfer();
        fileList.forEach(f => dt.items.add(f));
        document.getElementById('inputGambar').files = dt.files;
    }
    $('#inputGambar').on('change', function () { Array.from(this.files).forEach(f => fileList.push(f)); renderPreview(); });
    $(document).on('click', '.btn-remove-preview', function () { fileList.splice($(this).data('index'), 1); renderPreview(); });
    const dropArea = document.getElementById('dropArea');
    dropArea.addEventListener('dragover', e => { e.preventDefault(); dropArea.classList.add('drag-over'); });
    dropArea.addEventListener('dragleave', () => dropArea.classList.remove('drag-over'));
    dropArea.addEventListener('drop', e => {
        e.preventDefault(); dropArea.classList.remove('drag-over');
        Array.from(e.dataTransfer.files).forEach(f => { if (f.type.startsWith('image/')) fileList.push(f); });
        renderPreview();
    });
});
</script>
@endpush
