@extends('layouts.app')
@section('title', 'Tambah Daftar Hadir Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('attendance-forms.index') }}">Daftar Hadir Pelatihan</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Tambah</li>
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="d-sm-flex mb-3">
            <h1 class="h3 mb-0 text-gray-600">Formulir Daftar Hadir Pelatihan</h1>
        </div>

        {{-- Info auto-load --}}
        @if(count($defaultColumns) > 0)
        <div class="alert alert-info alert-dismissible fade show">
            <i class="ri-information-line me-2"></i>
            <strong>Kolom otomatis dimuat dari form terakhir:</strong>
            @foreach($defaultColumns as $col)
                <span class="badge bg-primary ms-1">{{ $col }}</span>
            @endforeach
            <br><small class="text-muted">Anda bisa menghapus, mengedit, atau menambah kolom baru sesuai kebutuhan.</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('attendance-forms.store') }}" method="POST" id="attendanceForm">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Error!</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Header --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-information-line me-2"></i>Informasi Pelatihan</h5>
                </div>
                <div class="card-body bg-light">
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label fw-bold">Topik Pelatihan <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="hidden" name="sop_id" value="{{ request('sop_id') }}">
                            <input type="text" name="topik_pelatihan" class="form-control"
                                   value="{{ old('topik_pelatihan') }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label fw-bold">Tanggal</label>
                        <div class="col-md-9">
                            <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal') }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label fw-bold">Tempat</label>
                        <div class="col-md-9">
                            <input type="text" name="tempat" class="form-control" value="{{ old('tempat') }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label fw-bold">Instruktur</label>
                        <div class="col-md-9">
                            <input type="text" name="instruktur" class="form-control" value="{{ old('instruktur') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Peserta --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="ri-table-line me-2"></i>Daftar Peserta</h5>
                            <small class="text-muted">Klik "+ Kolom" untuk menambah kolom baru, "+ Peserta" untuk baris baru</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnTambahKolom">
                                <i class="ri-layout-column-fill me-1"></i>+ Kolom
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="btnTambahPeserta">
                                <i class="ri-add-line me-1"></i>+ Peserta
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="participantsTable">
                            <thead class="table-light" id="tableHead">
                                <tr id="headerRow">
                                    <th width="50" class="text-center">No</th>
                                    <th width="220">Nama Karyawan</th>
                                    <th width="180">Jabatan</th>
                                    <th width="180">Lokasi Kerja</th>
                                    {{-- Kolom custom dari form terakhir --}}
                                    @foreach($defaultColumns as $ci => $label)
                                        <th class="text-center custom-th" data-col-index="{{ $ci }}" style="min-width:130px;">{{ $label }}</th>
                                    @endforeach
                                    <th width="80" class="text-center">Paraf</th>
                                    <th width="60" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="participantsBody">
                                @for($i = 0; $i < 5; $i++)
                                <tr class="participant-row">
                                    <td class="text-center row-number">{{ $i + 1 }}</td>
                                    <td><input type="text" name="participants[{{ $i }}][nama_karyawan]" class="form-control form-control-sm" placeholder="Nama lengkap"></td>
                                    <td><input type="text" name="participants[{{ $i }}][jabatan]" class="form-control form-control-sm" placeholder="Jabatan"></td>
                                    <td><input type="text" name="participants[{{ $i }}][lokasi_kerja]" class="form-control form-control-sm" placeholder="Lokasi kerja"></td>
                                    {{-- Input kolom custom dari form terakhir --}}
                                    @foreach($defaultColumns as $ci => $label)
                                        <td class="custom-td" data-col-index="{{ $ci }}">
                                            <input type="text" name="participants[{{ $i }}][custom][{{ $ci }}]" class="form-control form-control-sm" placeholder="{{ $label }}">
                                        </td>
                                    @endforeach
                                    <td class="text-center custom-paraf-cell"></td>
                                    <td class="text-center">
                                        @if($i >= 2)
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-row">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Panel Kolom Custom --}}
                <div class="card-footer bg-light" id="customColumnsPanel" style="{{ count($defaultColumns) > 0 ? 'display:block' : 'display:none' }}">
                    <p class="text-muted small fw-bold mb-2"><i class="ri-layout-column-line me-1"></i>Kolom Tambahan:</p>
                    <div class="d-flex flex-wrap gap-2" id="customColumnTags">
                        {{-- Tag kolom dari form terakhir --}}
                        @foreach($defaultColumns as $ci => $label)
                            <div class="col-tag" id="colTag{{ $ci }}">
                                <input type="hidden" name="custom_columns[{{ $ci }}]" value="{{ $label }}" class="custom-col-input" data-index="{{ $ci }}">
                                <span class="col-label">{{ $label }}</span>
                                <button type="button" class="btn-remove-col" data-col-index="{{ $ci }}" title="Hapus kolom">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <label class="form-label fw-bold">Catatan (Opsional)</label>
                    <textarea name="catatan" class="form-control" rows="3"
                              placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <a href="{{ route('attendance-forms.index') }}" class="btn btn-secondary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="ri-save-line me-1"></i>Simpan
                </button>
            </div>

        </form>
    </div>
</section>
@endsection

@push('styles')
<style>
    .table th, .table td { vertical-align: middle; font-size: 0.85rem; }
    .form-control-sm { font-size: 0.83rem; }
    .col-tag { display: flex; align-items: center; background: #e9ecef; border-radius: 6px; padding: 4px 10px; gap: 6px; font-size: 0.82rem; }
    .col-tag .btn-remove-col { background: none; border: none; color: #dc3545; padding: 0; font-size: 1rem; line-height: 1; cursor: pointer; }
    .col-tag .col-label { font-weight: 600; }
    .card { border-radius: 0.5rem; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {
    let participantIndex = 5;

    // Load kolom yang sudah ada dari backend (form terakhir)
    let customCols = @json($defaultColumns).map((label, i) => ({ label, index: i }));
    let nextColIndex = customCols.length;

    // =============================================
    // TAMBAH KOLOM CUSTOM BARU
    // =============================================
    $('#btnTambahKolom').on('click', function () {
        Swal.fire({
            title: 'Tambah Kolom',
            input: 'text',
            inputLabel: 'Nama kolom baru',
            inputPlaceholder: 'e.g. Divisi, No. HP, Status...',
            showCancelButton: true,
            confirmButtonText: 'Tambah',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0d6efd',
            inputValidator: (value) => {
                if (!value || !value.trim()) return 'Nama kolom tidak boleh kosong!';
                if (customCols.some(c => c && c.label.toLowerCase() === value.trim().toLowerCase()))
                    return 'Kolom dengan nama ini sudah ada!';
            }
        }).then(result => {
            if (result.isConfirmed) {
                addCustomColumn(result.value.trim(), nextColIndex);
                nextColIndex++;
            }
        });
    });

    function addCustomColumn(label, colIndex) {
        customCols.push({ label, index: colIndex });

        // Tambah <th>
        $(`<th class="text-center custom-th" data-col-index="${colIndex}" style="min-width:130px;">${label}</th>`)
            .insertBefore('#headerRow th:nth-last-child(2)');

        // Tambah <td> di setiap baris
        $('#participantsBody tr.participant-row').each(function (rowIdx) {
            const nameInput = $(this).find('input[name*="[nama_karyawan]"]').attr('name');
            const baseIdx   = nameInput ? nameInput.match(/\[(\d+)\]/)[1] : rowIdx;
            $(`<td class="custom-td" data-col-index="${colIndex}">
                <input type="text" name="participants[${baseIdx}][custom][${colIndex}]" class="form-control form-control-sm" placeholder="${label}">
            </td>`).insertBefore($(this).find('.custom-paraf-cell'));
        });

        // Tambah tag + hidden input
        $('#customColumnsPanel').show();
        $('#customColumnTags').append(`
            <div class="col-tag" id="colTag${colIndex}">
                <input type="hidden" name="custom_columns[${colIndex}]" value="${label}" class="custom-col-input" data-index="${colIndex}">
                <span class="col-label">${label}</span>
                <button type="button" class="btn-remove-col" data-col-index="${colIndex}" title="Hapus kolom">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        `);
    }

    // =============================================
    // HAPUS KOLOM CUSTOM
    // =============================================
    $(document).on('click', '.btn-remove-col', function () {
        const colIndex = $(this).data('col-index');

        Swal.fire({
            title: 'Hapus Kolom?',
            text: 'Semua data pada kolom ini akan hilang!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                // Hapus dari array
                customCols = customCols.map((c, i) => (c && c.index === colIndex) ? null : c);

                // Hapus hidden input
                $(`.custom-col-input[data-index="${colIndex}"]`).remove();

                // Hapus <th>
                $(`#headerRow .custom-th[data-col-index="${colIndex}"]`).remove();

                // Hapus <td> di semua baris
                $(`#participantsBody .custom-td[data-col-index="${colIndex}"]`).remove();

                // Hapus tag
                $(`#colTag${colIndex}`).remove();

                // Sembunyikan panel jika tidak ada kolom
                if ($('#customColumnTags .col-tag').length === 0) {
                    $('#customColumnsPanel').hide();
                }
            }
        });
    });

    // =============================================
    // TAMBAH BARIS PESERTA
    // =============================================
    $('#btnTambahPeserta').on('click', function () {
        let customTds = '';
        customCols.filter(Boolean).forEach(col => {
            customTds += `<td class="custom-td" data-col-index="${col.index}">
                <input type="text" name="participants[${participantIndex}][custom][${col.index}]" class="form-control form-control-sm" placeholder="${col.label}">
            </td>`;
        });

        const row = `
        <tr class="participant-row">
            <td class="text-center row-number"></td>
            <td><input type="text" name="participants[${participantIndex}][nama_karyawan]" class="form-control form-control-sm" placeholder="Nama lengkap"></td>
            <td><input type="text" name="participants[${participantIndex}][jabatan]" class="form-control form-control-sm" placeholder="Jabatan"></td>
            <td><input type="text" name="participants[${participantIndex}][lokasi_kerja]" class="form-control form-control-sm" placeholder="Lokasi kerja"></td>
            ${customTds}
            <td class="text-center custom-paraf-cell"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-row">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </td>
        </tr>`;
        $('#participantsBody').append(row);
        participantIndex++;
        updateRowNumbers();
    });

    // =============================================
    // HAPUS BARIS PESERTA
    // =============================================
    $(document).on('click', '.btn-hapus-row', function () {
        if ($('.participant-row').length > 1) {
            $(this).closest('tr').remove();
            updateRowNumbers();
        } else {
            Swal.fire({ icon: 'warning', title: 'Minimal 1 peserta!', timer: 2000, showConfirmButton: false });
        }
    });

    function updateRowNumbers() {
        $('.participant-row').each(function (i) {
            $(this).find('.row-number').text(i + 1);
        });
    }

    // =============================================
    // VALIDASI SUBMIT
    // =============================================
    $('#attendanceForm').on('submit', function (e) {
        let valid = false;
        $('input[name*="[nama_karyawan]"]').each(function () {
            if ($(this).val().trim() !== '') { valid = true; return false; }
        });
        if (!valid) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Minimal 1 peserta harus diisi!', timer: 2500, showConfirmButton: false });
        }
    });
});
</script>
@endpush