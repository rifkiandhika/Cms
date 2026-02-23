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
        <textarea name="note"
                  class="form-control auto-expand"
                  rows="1"
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
<h5 class="mb-3">Detail Barang</h5>

<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle" id="detail-table" style="min-width: 1400px;">
        <thead class="table-light">
            <tr>
                <th style="min-width:120px;">No Batch</th>
                <th style="min-width:160px;">Judul</th>
                <th style="min-width:160px;">Jenis <span class="text-danger">*</span></th>
                <th style="min-width:220px;">Nama Produk <span class="text-danger">*</span></th>
                <th style="min-width:130px;">Merk</th>
                <th style="min-width:130px;">Satuan <span class="text-danger">*</span></th>
                <th style="min-width:140px;">Exp Date</th>
                <th style="min-width:110px;">Stock Live</th>
                <th style="min-width:110px;">Stock PO</th>
                <th style="min-width:130px;">Min. Persediaan</th>
                <th style="min-width:140px;">Harga Beli</th>
                <th style="min-width:110px;">Kode Rak</th>
                <th style="min-width:90px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            {{--
                ROOT CAUSE FIX:
                - Saat CREATE: $supplier = new Supplier() → $supplier->exists = false
                  → detailSuppliers() mengembalikan empty collection, BUKAN null
                  → ?? [null] tidak terpicu → tbody kosong, tidak ada baris
                - Saat EDIT: $supplier->exists = true dan detailSuppliers berisi data

                Solusi: cek $supplier->exists secara eksplisit.
                Jika false (create mode) atau collection kosong → tampilkan 1 baris kosong [null].
            --}}
            @php
                $details = ($supplier->exists && $supplier->detailSuppliers->isNotEmpty())
                    ? $supplier->detailSuppliers
                    : collect([null]);
            @endphp

            @foreach($details as $detail)
            <tr class="supplier-detail-item" data-detail-id="{{ $detail->id ?? '' }}">

                <input type="hidden" name="detail_id[]" value="{{ $detail->id ?? '' }}">
                <input type="hidden" name="product_id[]" class="product-id-input" value="{{ $detail->product_id ?? '' }}">

                <td>
                    <textarea name="no_batch[]"
                              class="form-control auto-expand no-batch-input"
                              rows="1"
                              placeholder="e.g. BTC-36523">{{ $detail->no_batch ?? '' }}</textarea>
                </td>

                <td>
                    <textarea name="judul[]"
                              class="form-control auto-expand"
                              rows="1"
                              placeholder="e.g. Obat Sakit Kepala">{{ $detail->judul ?? '' }}</textarea>
                </td>

                <td>
                    <select name="jenis[]" class="form-select jenis-select" required>
                        <option value="" hidden>-- Pilih Jenis --</option>
                        @foreach($jenis as $j)
                            <option value="{{ $j->nama_jenis }}"
                                {{ ($detail->jenis ?? '') === $j->nama_jenis ? 'selected' : '' }}>
                                {{ $j->nama_jenis }}
                            </option>
                        @endforeach
                    </select>
                </td>

                <td>
                    <select class="form-select produk-select w-100"
                            style="{{ ($detail && $detail->product_id) ? '' : 'display: none;' }}"
                            {{ ($detail && $detail->product_id) ? '' : 'disabled' }}>
                        @if($detail && $detail->product_id && $detail->produk)
                            <option value="{{ $detail->produk->id }}" selected>
                                {{ $detail->produk->nama_produk }} ({{ $detail->produk->kode_produk }})
                            </option>
                        @else
                            <option value="">-- Pilih atau ketik untuk mencari --</option>
                        @endif
                    </select>

                    <input type="text"
                           name="nama_manual[]"
                           class="form-control nama-manual"
                           value="{{ ($detail && !$detail->product_id) ? ($detail->nama ?? '') : '' }}"
                           style="{{ ($detail && !$detail->product_id && ($detail->nama ?? '')) ? '' : 'display: none;' }}"
                           placeholder="Masukkan nama barang"
                           {{ ($detail && !$detail->product_id && ($detail->nama ?? '')) ? '' : 'disabled' }}>

                    {{-- Teks hint saat jenis belum dipilih --}}
                    <span class="nama-placeholder text-muted small"
                          style="{{ ($detail && ($detail->product_id || ($detail->nama ?? ''))) ? 'display:none;' : '' }}">
                        — Pilih jenis terlebih dahulu —
                    </span>
                </td>

                <td>
                    <textarea name="merk[]"
                              class="form-control auto-expand merk-input"
                              rows="1"
                              placeholder="e.g. Kimia Farma">{{ $detail->merk ?? '' }}</textarea>
                </td>

                <td>
                    <select name="satuan[]" class="form-select" required>
                        <option value="" hidden>-- Pilih Satuan --</option>
                        @foreach($satuans as $data)
                            <option value="{{ $data->nama_satuan }}"
                                {{ ($detail->satuan ?? '') == $data->nama_satuan ? 'selected' : '' }}>
                                {{ $data->nama_satuan }}
                            </option>
                        @endforeach
                    </select>
                </td>

                <td>
                    <input type="date" name="exp_date[]" class="form-control"
                           value="{{ $detail->exp_date ?? '' }}">
                </td>

                <td>
                    <input type="number" name="stock_live[]" class="form-control"
                           value="{{ $detail->stock_live ?? '' }}" placeholder="e.g. 50" min="0">
                </td>

                <td>
                    <input type="number" name="stock_po[]" class="form-control"
                           value="{{ $detail->stock_po ?? '' }}" placeholder="e.g. 20" min="0">
                </td>

                <td>
                    <input type="number" name="min_persediaan[]" class="form-control"
                           value="{{ $detail->min_persediaan ?? '' }}" placeholder="e.g. 10" min="0">
                </td>

                <td>
                    <input type="text"
                           name="harga_beli[]"
                           class="form-control format-rupiah"
                           value="{{ $detail ? number_format($detail->harga_beli ?? 0, 0, ',', '.') : '' }}"
                           placeholder="e.g. 500.000">
                </td>

                <td>
                    <input type="text" name="kode_rak[]" class="form-control"
                           value="{{ $detail->kode_rak ?? '' }}" placeholder="e.g. A12">
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
            @endforeach
        </tbody>
    </table>
</div>

@push('styles')
<style>
    .select2-container .select2-selection--single {
        height: 37px !important;
        padding-top: 3px;
        border: 1px solid #ced4da;
        border-radius: 6px;
    }
    .select2-selection__rendered {
        line-height: 26px !important;
    }
    .table {
        border: 1px solid #ced4da !important;
    }
    textarea.auto-expand {
        resize: none;
        overflow: hidden;
        min-height: 37px;
        line-height: 1.5;
        box-sizing: border-box;
        width: 100%;
    }
    #detail-table td {
        padding: 6px 8px;
        vertical-align: middle;
    }
    #detail-table th {
        white-space: nowrap;
        font-size: 0.85rem;
    }
    .nama-placeholder {
        display: block;
        padding: 4px 2px;
        font-size: 0.82rem;
        color: #adb5bd;
        font-style: italic;
    }
</style>
@endpush

@push('scripts')
<script>
    window.produkApiUrl = "{{ route('api.produk.search') }}";

    window.jenisOptions = `
        <option value="" hidden>-- Pilih Jenis --</option>
        @foreach($jenis as $j)
            <option value="{{ $j->nama_jenis }}">{{ $j->nama_jenis }}</option>
        @endforeach
    `;

    window.satuanOptions = `
        <option value="" hidden>-- Pilih Satuan --</option>
        @foreach($satuans as $s)
            <option value="{{ $s->nama_satuan }}">{{ $s->nama_satuan }}</option>
        @endforeach
    `;
</script>

<script>
    function autoExpandTextarea(el) {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }

    function initAutoExpand(scope) {
        const textareas = (scope || document).querySelectorAll('textarea.auto-expand');
        textareas.forEach(function(ta) {
            autoExpandTextarea(ta);
            if (!ta.dataset.autoExpandInit) {
                ta.dataset.autoExpandInit = '1';
                ta.addEventListener('input', function() {
                    autoExpandTextarea(this);
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initAutoExpand();
    });
</script>

<script>
    function initFormatRupiah(scope) {
        const inputs = (scope || document).querySelectorAll('.format-rupiah');
        inputs.forEach(function(input) {
            if (input.dataset.rupiahInit) return;
            input.dataset.rupiahInit = '1';
            input.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                this.value = new Intl.NumberFormat('id-ID').format(value);
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

    function initProdukSelect2(el) {
        const $row  = el.closest('.supplier-detail-item');
        const jenis = $row.find('.jenis-select').val();

        if (!jenis || jenis === 'Lainnya') return;
        if (el.hasClass('select2-hidden-accessible')) return;

        el.select2({
            placeholder: '-- Pilih atau ketik untuk mencari --',
            allowClear: true,
            width: '100%',
            dropdownAutoWidth: true,
            ajax: {
                url: window.produkApiUrl,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term, jenis: jenis, page: params.page || 1 };
                },
                processResults: function(data) {
                    return {
                        results: (data.items || []).map(function(item) {
                            return {
                                id: item.id,
                                text: item.text,
                                kode_produk: item.kode_produk,
                                nama_produk: item.nama_produk,
                                merk: item.merk || '',
                                satuan: item.satuan || '',
                                harga_beli: item.harga_beli || 0
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

    // Init Select2 untuk baris yang sudah ada saat page load (mode EDIT)
    $('.produk-select').each(function() {
        const $select = $(this);
        const $row    = $select.closest('.supplier-detail-item');
        const jenis   = $row.find('.jenis-select').val();

        if (jenis && jenis !== 'Lainnya') {
            $select.show().prop('disabled', false);
            initProdukSelect2($select);
        }
    });

    // =========================================================
    // Handle perubahan Jenis
    // =========================================================
    $('#detail-table').on('change', '.jenis-select', function() {
        const $row          = $(this).closest('.supplier-detail-item');
        const jenis         = $(this).val();
        const $produkSelect = $row.find('.produk-select');
        const $namaManual   = $row.find('.nama-manual');
        const $placeholder  = $row.find('.nama-placeholder');

        $row.find('.product-id-input').val('');

        if ($produkSelect.hasClass('select2-hidden-accessible')) {
            $produkSelect.val(null).trigger('change').select2('destroy');
        }
        $produkSelect.val('');
        $namaManual.val('');

        if (!jenis) {
            $produkSelect.hide().prop('disabled', true).prop('required', false)
                .html('<option value="">-- Pilih Jenis Terlebih Dahulu --</option>');
            $namaManual.hide().prop('disabled', true).prop('required', false);
            $placeholder.show();
        } else if (jenis === 'Lainnya') {
            $produkSelect.hide().prop('disabled', true).prop('required', false);
            $namaManual.show().prop('disabled', false).prop('required', true).focus();
            $placeholder.hide();
        } else {
            $produkSelect.show().prop('disabled', false).prop('required', true)
                .html('<option value="">-- Pilih atau ketik untuk mencari --</option>');
            $namaManual.hide().prop('disabled', true).prop('required', false);
            $placeholder.hide();
            initProdukSelect2($produkSelect);
        }
    });

    // =========================================================
    // Saat produk dipilih — auto-fill field terkait
    // =========================================================
    $('#detail-table').on('select2:select', '.produk-select', function(e) {
        const data = e.params.data;
        const $row = $(this).closest('.supplier-detail-item');

        $row.find('.product-id-input').val(data.id);

        if (data.kode_produk) {
            const $noBatch = $row.find('.no-batch-input');
            $noBatch.val(data.kode_produk);
            autoExpandTextarea($noBatch[0]);
        }

        if (data.merk) {
            const $merk = $row.find('.merk-input');
            $merk.val(data.merk);
            autoExpandTextarea($merk[0]);
        }

        if (data.satuan) {
            $row.find('select[name="satuan[]"]').val(data.satuan);
        }

        if (data.harga_beli) {
            $row.find('input[name="harga_beli[]"]').val(
                new Intl.NumberFormat('id-ID').format(data.harga_beli)
            );
        }
    });

    // Clear saat Select2 di-clear
    $('#detail-table').on('select2:clear', '.produk-select', function() {
        const $row = $(this).closest('.supplier-detail-item');
        $row.find('.product-id-input').val('');
        const $noBatch = $row.find('.no-batch-input');
        $noBatch.val('');
        autoExpandTextarea($noBatch[0]);
    });

    $('#detail-table').on('input', '.nama-manual', function() {
        $(this).closest('.supplier-detail-item').find('.product-id-input').val('');
    });

    // =========================================================
    // Tambah Baris Baru
    // =========================================================
    $('#detail-table').on('click', '.btn-add', function() {
        const $tbody        = $('#detail-table tbody');
        const jenisOptions  = window.jenisOptions  || '<option value="">-- Pilih Jenis --</option>';
        const satuanOptions = window.satuanOptions || '<option value="">-- Pilih Satuan --</option>';

        const $newRow = $(`
            <tr class="supplier-detail-item" data-detail-id="">
                <input type="hidden" name="detail_id[]" value="">
                <input type="hidden" name="product_id[]" class="product-id-input" value="">

                <td><textarea name="no_batch[]" class="form-control auto-expand no-batch-input" rows="1" placeholder="e.g. BTC-36523"></textarea></td>
                <td><textarea name="judul[]" class="form-control auto-expand" rows="1" placeholder="e.g. Obat Sakit Kepala"></textarea></td>
                <td>
                    <select name="jenis[]" class="form-select jenis-select" required>
                        ${jenisOptions}
                    </select>
                </td>
                <td>
                    <select class="form-select produk-select w-100" style="display:none;" disabled>
                        <option value="">-- Pilih Jenis Terlebih Dahulu --</option>
                    </select>
                    <input type="text" name="nama_manual[]" class="form-control nama-manual" style="display:none;" placeholder="Masukkan nama barang" disabled>
                    <span class="nama-placeholder text-muted small" style="font-style:italic;">— Pilih jenis terlebih dahulu —</span>
                </td>
                <td><textarea name="merk[]" class="form-control auto-expand merk-input" rows="1" placeholder="e.g. Kimia Farma"></textarea></td>
                <td>
                    <select name="satuan[]" class="form-select" required>
                        ${satuanOptions}
                    </select>
                </td>
                <td><input type="date" name="exp_date[]" class="form-control"></td>
                <td><input type="number" name="stock_live[]" class="form-control" placeholder="e.g. 50" min="0"></td>
                <td><input type="number" name="stock_po[]" class="form-control" placeholder="e.g. 20" min="0"></td>
                <td><input type="number" name="min_persediaan[]" class="form-control" placeholder="e.g. 10" min="0"></td>
                <td><input type="text" name="harga_beli[]" class="form-control format-rupiah" placeholder="e.g. 5.000"></td>
                <td><input type="text" name="kode_rak[]" class="form-control" placeholder="e.g. A12"></td>
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

        $tbody.append($newRow);
        initAutoExpand($newRow[0]);
        initFormatRupiah($newRow[0]);
    });

    // =========================================================
    // Hapus Baris
    // =========================================================
    $('#detail-table').on('click', '.btn-remove', function() {
        if ($('#detail-table tbody tr').length > 1) {
            const $row    = $(this).closest('tr');
            const $select = $row.find('.produk-select');
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $row.remove();
        } else {
            alert('Minimal satu baris detail harus ada.');
        }
    });

});
</script>
@endpush