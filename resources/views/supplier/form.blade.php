@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Terdapat kesalahan:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ============================== DATA SUPPLIER ============================== --}}
<h5 class="mb-3">Data Supplier</h5>

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
        <input type="text" name="nama_supplier"
               class="form-control @error('nama_supplier') is-invalid @enderror"
               value="{{ old('nama_supplier', $supplier->nama_supplier ?? '') }}"
               placeholder="e.g. PT Premiere Alkes Nusindo" required>
        @error('nama_supplier')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">NPWP</label>
        <input type="text" name="npwp"
               class="form-control @error('npwp') is-invalid @enderror"
               value="{{ old('npwp', $supplier->npwp ?? '') }}"
               placeholder="e.g. 01.234.567.8-901.000">
        @error('npwp')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Izin Operasional</label>
        <input type="text" name="izin_operasional"
               class="form-control @error('izin_operasional') is-invalid @enderror"
               value="{{ old('izin_operasional', $supplier->izin_operasional ?? '') }}"
               placeholder="e.g. 123/SIK/2024">
        @error('izin_operasional')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Nama Kontak</label>
        <input type="text" name="kontak_person"
               class="form-control @error('kontak_person') is-invalid @enderror"
               value="{{ old('kontak_person', $supplier->kontak_person ?? '') }}"
               placeholder="e.g. Budi Santoso">
        @error('kontak_person')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $supplier->email ?? '') }}"
               placeholder="e.g. supplier@email.com">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">No. Telepon</label>
        <input type="text" name="no_telp"
               class="form-control @error('no_telp') is-invalid @enderror"
               value="{{ old('no_telp', $supplier->no_telp ?? '') }}"
               placeholder="e.g. 0812-3456-7890">
        @error('no_telp')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label class="form-label">Alamat</label>
        <textarea name="alamat"
                  class="form-control @error('alamat') is-invalid @enderror"
                  rows="2"
                  placeholder="e.g. Jl. Raya Randuagung No. 10, Singosari">{{ old('alamat', $supplier->alamat ?? '') }}</textarea>
        @error('alamat')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-label">Kota</label>
        <input type="text" name="kota"
               class="form-control @error('kota') is-invalid @enderror"
               value="{{ old('kota', $supplier->kota ?? '') }}"
               placeholder="e.g. Malang">
        @error('kota')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Provinsi</label>
        <input type="text" name="provinsi"
               class="form-control @error('provinsi') is-invalid @enderror"
               value="{{ old('provinsi', $supplier->provinsi ?? '') }}"
               placeholder="e.g. Jawa Timur">
        @error('provinsi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="Aktif"    {{ old('status', $supplier->status ?? 'Aktif') == 'Aktif'    ? 'selected' : '' }}>Aktif</option>
            <option value="Nonaktif" {{ old('status', $supplier->status ?? '') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Catatan</label>
        <textarea name="note" class="form-control auto-expand" rows="1"
                  placeholder="e.g. kurang KTP supplier">{{ old('note', $supplier->note ?? '') }}</textarea>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <label class="form-label">Upload Dokumen 1 (PDF)</label>
        <input type="file" name="file" accept="application/pdf" class="form-control">
        @if(isset($supplier) && $supplier->file)
            <small class="mt-1 d-inline-block">
                <a href="{{ asset($supplier->file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="ri-file-pdf-line"></i> Lihat Dokumen 1
                </a>
            </small>
        @endif
        @error('file')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Upload Dokumen 2 (PDF)</label>
        <input type="file" name="file2" accept="application/pdf" class="form-control">
        @if(isset($supplier) && $supplier->file2)
            <small class="mt-1 d-inline-block">
                <a href="{{ asset($supplier->file2) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="ri-file-pdf-line"></i> Lihat Dokumen 2
                </a>
            </small>
        @endif
        @error('file2')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- ============================== DETAIL BARANG ============================== --}}
<h5 class="mb-1">Detail Barang</h5>
<p class="text-muted small mb-3">
    <i class="ri-information-line"></i>
    Pilih <strong>Jenis</strong> terlebih dahulu, kemudian pilih <strong>Produk</strong>,
    lalu pilih <strong>Satuan Produk</strong>. Field <strong>Isi</strong> akan terisi otomatis.
    Lengkapi <strong>Harga Beli</strong> per satuan tersebut.
</p>

<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle" id="detail-table" style="min-width: 900px;">
        <thead class="table-light">
            <tr>
                <th style="min-width:160px;">Jenis <span class="text-danger">*</span></th>
                <th style="min-width:220px;">Nama Produk <span class="text-danger">*</span></th>
                <th style="min-width:180px;">Satuan Produk <span class="text-danger">*</span></th>
                <th style="min-width:90px;">Isi<br><small class="fw-normal text-muted">qty satuan dasar</small></th>
                <th style="min-width:160px;">Harga Beli <span class="text-danger">*</span></th>
                <th style="min-width:200px;">Catatan</th>
                <th style="min-width:90px;">Status</th>
                <th style="min-width:90px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Untuk form create: $supplier baru (tidak ada detail), tampilkan 1 baris kosong
                // Untuk form edit: tampilkan detail yang sudah ada
                $details = ($supplier->exists && $supplier->detailSuppliers->isNotEmpty())
                    ? $supplier->detailSuppliers
                    : collect([null]);
            @endphp

            @foreach($details as $detail)
            <tr class="supplier-detail-item">

                {{-- Hidden inputs: ID relasi --}}
                <input type="hidden" name="detail_id[]"         value="{{ $detail->id ?? '' }}">
                <input type="hidden" name="produk_id[]"         class="produk-id-input"         value="{{ $detail->produk_id ?? '' }}">
                <input type="hidden" name="produk_satuan_id[]"  class="produk-satuan-id-input"  value="{{ $detail->produk_satuan_id ?? '' }}">

                {{-- JENIS --}}
                <td>
                    <select name="jenis[]" class="form-select jenis-select">
                        <option value="" hidden>-- Pilih Jenis --</option>
                        @foreach($jenis as $j)
                            <option value="{{ $j->nama_jenis }}"
                                {{ ($detail->produk->jenis ?? '') === $j->nama_jenis ? 'selected' : '' }}>
                                {{ $j->nama_jenis }}
                            </option>
                        @endforeach
                    </select>
                </td>

                {{-- NAMA PRODUK --}}
                <td>
                    {{-- Select2 — ditampilkan jika sudah ada produk_id --}}
                    <select class="form-select produk-select w-100"
                            style="{{ ($detail && $detail->produk_id) ? '' : 'display: none;' }}"
                            {{ ($detail && $detail->produk_id) ? '' : 'disabled' }}>
                        @if($detail && $detail->produk_id && $detail->produk)
                            <option value="{{ $detail->produk->id }}" selected>
                                {{ $detail->produk->nama_produk }} ({{ $detail->produk->kode_produk }})
                            </option>
                        @else
                            <option value="">-- Pilih atau ketik untuk mencari --</option>
                        @endif
                    </select>
                    {{-- Placeholder sebelum jenis dipilih --}}
                    <span class="nama-placeholder text-muted small"
                          style="{{ ($detail && $detail->produk_id) ? 'display:none;' : '' }}">
                        — Pilih jenis terlebih dahulu —
                    </span>
                </td>

                {{-- SATUAN PRODUK --}}
                <td>
                    <select class="form-select satuan-produk-select"
                            {{ ($detail && $detail->produk_id) ? '' : 'disabled' }}>
                        <option value="" hidden>-- Pilih Satuan --</option>
                        @if($detail && $detail->produk && $detail->produk->produkSatuans)
                            @foreach($detail->produk->produkSatuans as $ps)
                                <option value="{{ $ps->id }}" data-isi="{{ $ps->isi }}"
                                    {{ ($detail->produk_satuan_id ?? '') == $ps->id ? 'selected' : '' }}>
                                    {{ $ps->label }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <small class="satuan-hint text-muted">
                        @if($detail && $detail->produkSatuan)
                            1 {{ $detail->produkSatuan->label }} = {{ $detail->produkSatuan->isi }} satuan dasar
                        @endif
                    </small>
                </td>

                {{-- ISI (readonly, diisi otomatis dari data-isi satuan) --}}
                <td>
                    <input type="number" name="isi[]" class="form-control isi-input"
                           value="{{ $detail->produkSatuan->isi ?? 1 }}"
                           min="1" step="1" placeholder="1" readonly>
                    <small class="text-muted">satuan dasar</small>
                </td>

                {{-- HARGA BELI --}}
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp</span>
                        <input type="text" name="harga_beli[]" class="form-control format-rupiah"
                               value="{{ $detail ? number_format($detail->harga_beli ?? 0, 0, ',', '.') : '' }}"
                               placeholder="0">
                    </div>
                </td>

                {{-- CATATAN (kolom catatan di detail_suppliers) --}}
                <td>
                    <textarea name="catatan[]" class="form-control auto-expand"
                              rows="1" placeholder="e.g. Harga berlaku s/d Des 2025">{{ $detail->catatan ?? '' }}</textarea>
                </td>

                {{-- IS_AKTIF --}}
                <td class="text-center">
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input class="form-check-input" type="checkbox" name="is_aktif[]"
                               value="1" {{ ($detail->is_aktif ?? true) ? 'checked' : '' }}>
                    </div>
                    <small class="text-muted d-block">Aktif</small>
                </td>

                {{-- AKSI --}}
                <td class="text-center">
                    <div class="d-flex flex-column justify-content-center align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success btn-add" title="Tambah Baris">
                            <i class="ri-add-line"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove" title="Hapus Baris">
                            <i class="ri-subtract-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('styles')
<style>
    .select2-container .select2-selection--single {
        height: 37px !important; padding-top: 3px;
        border: 1px solid #ced4da; border-radius: 6px;
    }
    .select2-selection__rendered { line-height: 26px !important; }
    .table { border: 1px solid #ced4da !important; }
    textarea.auto-expand {
        resize: none; overflow: hidden;
        min-height: 37px; line-height: 1.5; box-sizing: border-box; width: 100%;
    }
    #detail-table td { padding: 6px 8px; vertical-align: middle; }
    #detail-table th { white-space: nowrap; font-size: 0.85rem; }
    .nama-placeholder {
        display: block; padding: 4px 2px;
        font-size: 0.82rem; color: #adb5bd; font-style: italic;
    }
    .satuan-hint { display: block; font-size: 0.78rem; color: #6c757d; margin-top: 2px; }
    .isi-input[readonly] { background-color: #f8f9fa; }
</style>
@endpush

@push('scripts')
<script>
    window.produkApiUrl    = "{{ route('api.produk.search') }}";
    window.produkSatuanUrl = "{{ route('api.produk.satuans') }}";

    window.jenisOptions = `
        <option value="" hidden>-- Pilih Jenis --</option>
        @foreach($jenis as $j)
            <option value="{{ $j->nama_jenis }}">{{ $j->nama_jenis }}</option>
        @endforeach
    `;
</script>

<script>
    function autoExpandTextarea(el) {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }
    function initAutoExpand(scope) {
        (scope || document).querySelectorAll('textarea.auto-expand').forEach(function(ta) {
            autoExpandTextarea(ta);
            if (!ta.dataset.autoExpandInit) {
                ta.dataset.autoExpandInit = '1';
                ta.addEventListener('input', function() { autoExpandTextarea(this); });
            }
        });
    }
    document.addEventListener('DOMContentLoaded', function() { initAutoExpand(); });
</script>

<script>
    function initFormatRupiah(scope) {
        (scope || document).querySelectorAll('.format-rupiah').forEach(function(input) {
            if (input.dataset.rupiahInit) return;
            input.dataset.rupiahInit = '1';
            input.addEventListener('input', function() {
                this.value = new Intl.NumberFormat('id-ID').format(this.value.replace(/\D/g, ''));
            });
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        initFormatRupiah();
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                document.querySelectorAll('.format-rupiah').forEach(function(inp) {
                    inp.value = inp.value.replace(/\./g, '');
                });
            });
        });
    });
</script>

<script>
$(document).ready(function() {

    // =========================================================
    // HELPER: isi dropdown satuan dari array produk_satuans
    // =========================================================
    function populateSatuanProduk($row, satuans, selectedId) {
        const $sel  = $row.find('.satuan-produk-select');
        const $isi  = $row.find('.isi-input');
        const $hint = $row.find('.satuan-hint');
        const $psId = $row.find('.produk-satuan-id-input');

        $sel.html('<option value="" hidden>-- Pilih Satuan --</option>');

        if (!satuans || satuans.length === 0) {
            $sel.prop('disabled', true);
            $hint.text('Produk ini belum memiliki satuan');
            return;
        }

        satuans.forEach(function(ps) {
            const selected = (selectedId && ps.id == selectedId) ? 'selected' : '';
            $sel.append(`<option value="${ps.id}" data-isi="${ps.isi}" ${selected}>${ps.label}</option>`);
        });
        $sel.prop('disabled', false);

        // Auto-pilih default jika belum ada selectedId
        if (!selectedId) {
            const def = satuans.find(ps => ps.is_default);
            if (def) $sel.val(def.id);
        }

        // Bind change — update isi & hidden produk_satuan_id
        $sel.off('change.satuan').on('change.satuan', function() {
            const $opt = $(this).find('option:selected');
            const isi  = parseInt($opt.data('isi')) || 1;
            $isi.val(isi);
            $psId.val($(this).val());
            $hint.text($(this).val() ? `1 ${$opt.text().trim()} = ${isi} satuan dasar` : '');
        });

        $sel.trigger('change.satuan');
    }

    // =========================================================
    // INISIALISASI Select2 pada produk-select
    // =========================================================
    function initProdukSelect2($el) {
        const $row  = $el.closest('.supplier-detail-item');
        const jenis = $row.find('.jenis-select').val();
        if (!jenis) return;
        if ($el.hasClass('select2-hidden-accessible')) return;

        $el.select2({
            placeholder: '-- Pilih atau ketik untuk mencari --',
            allowClear: true, width: '100%', dropdownAutoWidth: true,
            ajax: {
                url: window.produkApiUrl, dataType: 'json', delay: 250,
                data: function(params) {
                    return { q: params.term, jenis: jenis, page: params.page || 1 };
                },
                processResults: function(data) {
                    return {
                        results: (data.items || []).map(function(item) {
                            return {
                                id:              item.id,
                                text:            item.text,
                                kode_produk:     item.kode_produk,
                                produk_satuans:  item.produk_satuans || []
                            };
                        }),
                        pagination: { more: data.pagination ? data.pagination.more : false }
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });
    }

    // Init Select2 untuk baris edit yang sudah punya produk_id
    $('.supplier-detail-item').each(function() {
        const $row    = $(this);
        const $sel    = $row.find('.produk-select');
        const produkId = $row.find('.produk-id-input').val();
        const psId    = $row.find('.produk-satuan-id-input').val();

        if (produkId) {
            $sel.show().prop('disabled', false);
            initProdukSelect2($sel);
            // Ambil satuan untuk produk ini agar dropdown satuan terisi
            $.get(window.produkSatuanUrl, { produk_id: produkId }, function(data) {
                populateSatuanProduk($row, data.satuans || [], psId);
            });
        }
    });

    // =========================================================
    // Handle perubahan Jenis
    // =========================================================
    $('#detail-table').on('change', '.jenis-select', function() {
        const $row    = $(this).closest('.supplier-detail-item');
        const jenis   = $(this).val();
        const $ps     = $row.find('.produk-select');
        const $ph     = $row.find('.nama-placeholder');
        const $satuan = $row.find('.satuan-produk-select');

        // Reset produk & satuan
        $row.find('.produk-id-input').val('');
        $row.find('.produk-satuan-id-input').val('');
        if ($ps.hasClass('select2-hidden-accessible')) { $ps.val(null).trigger('change').select2('destroy'); }
        $ps.val('');
        $satuan.html('<option value="" hidden>-- Pilih Satuan --</option>').prop('disabled', true);
        $row.find('.satuan-hint').text('');
        $row.find('.isi-input').val(1);

        if (!jenis) {
            $ps.hide().prop('disabled', true)
               .html('<option value="">-- Pilih Jenis Terlebih Dahulu --</option>');
            $ph.show();
        } else {
            $ps.show().prop('disabled', false)
               .html('<option value="">-- Pilih atau ketik untuk mencari --</option>');
            $ph.hide();
            initProdukSelect2($ps);
        }
    });

    // =========================================================
    // Saat produk dipilih → populate satuan
    // =========================================================
    $('#detail-table').on('select2:select', '.produk-select', function(e) {
        const data = e.params.data;
        const $row = $(this).closest('.supplier-detail-item');

        $row.find('.produk-id-input').val(data.id);
        $row.find('.produk-satuan-id-input').val('');

        populateSatuanProduk($row, data.produk_satuans || [], null);
    });

    // Saat produk di-clear
    $('#detail-table').on('select2:clear', '.produk-select', function() {
        const $row = $(this).closest('.supplier-detail-item');
        $row.find('.produk-id-input').val('');
        $row.find('.produk-satuan-id-input').val('');
        $row.find('.satuan-produk-select')
            .html('<option value="" hidden>-- Pilih Satuan --</option>').prop('disabled', true);
        $row.find('.satuan-hint').text('');
        $row.find('.isi-input').val(1);
    });

    // =========================================================
    // Tambah Baris Baru
    // =========================================================
    $('#detail-table').on('click', '.btn-add', function() {
        const jenisOptions = window.jenisOptions || '<option value="">-- Pilih Jenis --</option>';

        const $newRow = $(`
            <tr class="supplier-detail-item">
                <input type="hidden" name="detail_id[]" value="">
                <input type="hidden" name="produk_id[]" class="produk-id-input" value="">
                <input type="hidden" name="produk_satuan_id[]" class="produk-satuan-id-input" value="">

                <td>
                    <select name="jenis[]" class="form-select jenis-select">
                        ${jenisOptions}
                    </select>
                </td>
                <td>
                    <select class="form-select produk-select w-100" style="display:none;" disabled>
                        <option value="">-- Pilih Jenis Terlebih Dahulu --</option>
                    </select>
                    <span class="nama-placeholder text-muted small" style="font-style:italic;">
                        — Pilih jenis terlebih dahulu —
                    </span>
                </td>
                <td>
                    <select class="form-select satuan-produk-select" disabled>
                        <option value="" hidden>-- Pilih Satuan --</option>
                    </select>
                    <small class="satuan-hint text-muted"></small>
                </td>
                <td>
                    <input type="number" name="isi[]" class="form-control isi-input"
                           value="1" min="1" step="1" placeholder="1" readonly>
                    <small class="text-muted">satuan dasar</small>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp</span>
                        <input type="text" name="harga_beli[]" class="form-control format-rupiah" placeholder="0">
                    </div>
                </td>
                <td>
                    <textarea name="catatan[]" class="form-control auto-expand" rows="1"
                              placeholder="e.g. Harga berlaku s/d Des 2025"></textarea>
                </td>
                <td class="text-center">
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input class="form-check-input" type="checkbox" name="is_aktif[]" value="1" checked>
                    </div>
                    <small class="text-muted d-block">Aktif</small>
                </td>
                <td class="text-center">
                    <div class="d-flex flex-column justify-content-center align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success btn-add" title="Tambah Baris">
                            <i class="ri-add-line"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove" title="Hapus Baris">
                            <i class="ri-subtract-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `);

        $('#detail-table tbody').append($newRow);
        initAutoExpand($newRow[0]);
        initFormatRupiah($newRow[0]);
    });

    // =========================================================
    // Hapus Baris
    // =========================================================
    $('#detail-table').on('click', '.btn-remove', function() {
        if ($('#detail-table tbody tr').length > 1) {
            const $row = $(this).closest('tr');
            const $sel = $row.find('.produk-select');
            if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
            $row.remove();
        } else {
            alert('Minimal satu baris detail harus ada.');
        }
    });

});
</script>
@endpush