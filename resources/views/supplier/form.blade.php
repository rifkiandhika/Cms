<h5 class="mb-3">Data Supplier</h5>

<div class="row mb-3">
    <div class="col-md-4">
        <label>NPWP</label>
        <input type="text" name="npwp" class="form-control" placeholder="e.g. 000.00-01-000-222"
            value="{{ old('npwp', $supplier->npwp ?? '') }}">
    </div>
    <div class="col-md-4">
        <label>Supplier <span class="text-danger">*</span></label>
        <input type="text" name="nama_supplier" class="form-control" placeholder="e.g. PT Premiere Alkes Nusindo"
            value="{{ old('nama_supplier', $supplier->nama_supplier ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label>Alamat</label>
        <input type="text" name="alamat" class="form-control" placeholder="e.g. Randuagung, Singosari"
            value="{{ old('alamat', $supplier->alamat ?? '') }}">
    </div>
    <div class="col-md-4 mt-2">
        <label>Upload Dokumen 1 (PDF)</label>
        <input type="file" name="file" accept="application/pdf" class="form-control">
        @if(isset($supplier) && $supplier->file)
            <small class="text-primary btn btn-sm btn-outline-primary mt-1">
                <a href="{{ asset($supplier->file) }}" target="_blank">Lihat Dokumen 1</a>
            </small>
        @endif
    </div>
    <div class="col-md-4 mt-2">
        <label>Upload Dokumen 2 (PDF)</label>
        <input type="file" name="file2" accept="application/pdf" class="form-control">
        @if(isset($supplier) && $supplier->file2)
            <small class="text-primary btn btn-sm btn-outline-primary mt-1">
                <a href="{{ asset($supplier->file2) }}" target="_blank">Lihat Dokumen 2</a>
            </small>
        @endif
    </div>
    <div class="col-md-4 mt-2">
        <label>Catatan</label>
        <textarea 
            class="form-control" 
            name="note" 
            cols="30" 
            rows="1" 
            placeholder="e.g. kurang KTP supplier">{{ old('note', $supplier->note ?? '') }}</textarea>
    </div>
</div>

<h5 class="mb-3">Detail Barang</h5>

<table class="table table-bordered table-striped" id="detail-table">
    <thead>
        <tr>
            <th width="85%">Data Detail</th>
            <th width="15%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($supplier->detailSuppliers ?? [null] as $detail)
        <tr class="supplier-detail-item" data-detail-id="{{ $detail->id ?? '' }}">
            <td>
                <input type="hidden" name="detail_id[]" value="{{ $detail->id ?? '' }}">
                <input type="hidden" name="product_id[]" class="product-id-input" value="{{ $detail->product_id ?? '' }}">
                
                <div class="row g-2">
                    <div class="col-md-6">
                        <label>No Batch</label>
                        <input type="text" name="no_batch[]" class="form-control"
                               value="{{ $detail->no_batch ?? '' }}" placeholder="e.g. BTC-36523">
                    </div>
                    <div class="col-md-6">
                        <label>Judul</label>
                        <input type="text" name="judul[]" class="form-control"
                               value="{{ $detail->judul ?? '' }}" placeholder="e.g. Obat Sakit Kepala">
                    </div>
                    <div class="col-md-6">
                        <label>Jenis <span class="text-danger">*</span></label>
                        <select name="jenis[]" class="form-select jenis-select" required>
                            <option value="" hidden>-- Pilih Jenis --</option>
                            @foreach($jenis as $j)
                                <option value="{{ $j->nama_jenis }}" {{ ($detail->jenis ?? '') == $j->nama_jenis ? 'selected' : '' }}>
                                    {{ $j->nama_jenis }}
                                </option>
                            @endforeach
                            {{-- <option value="Lainnya" {{ ($detail->jenis ?? '') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option> --}}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Nama Produk <span class="text-danger">*</span></label>
                        
                        <!-- Select2 untuk Produk dari Database -->
                        <select class="form-select produk-select" style="{{ ($detail->product_id ?? '') ? '' : 'display: none;' }}">
                            @if($detail && $detail->product_id && $detail->produk)
                                <option value="{{ $detail->produk->id }}" selected>
                                    {{ $detail->produk->nama_produk }} ({{ $detail->produk->kode_produk }})
                                </option>
                            @else
                                <option value="">-- Pilih atau ketik untuk mencari --</option>
                            @endif
                        </select>
                        
                        {{-- <!-- Input Manual untuk jenis lainnya atau produk baru -->
                        <input type="text" 
                               name="nama_manual[]" 
                               class="form-control nama-manual" 
                               value="{{ (!$detail->product_id && $detail) ? ($detail->nama ?? '') : '' }}"
                               style="{{ (!$detail->product_id && $detail) ? '' : 'display: none;' }}" 
                               placeholder="Masukkan nama barang"
                               {{ (!$detail->product_id && $detail) ? 'required' : 'disabled' }}> --}}
                    </div>
                    <div class="col-md-6">
                        <label>Merk</label>
                        <input type="text" name="merk[]" class="form-control merk-input"
                               value="{{ $detail->merk ?? '' }}" placeholder="e.g. Kimia Farma">
                    </div>
                    <div class="col-md-6">
                        <label>Satuan <span class="text-danger">*</span></label>
                        <select name="satuan[]" class="form-select" required>
                            <option value="" hidden>-- Pilih Satuan --</option>
                            @foreach($satuans as $data)
                                <option value="{{ $data->nama_satuan }}" {{ ($detail->satuan ?? '') == $data->nama_satuan ? 'selected' : '' }}>
                                    {{ $data->nama_satuan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Exp Date</label>
                        <input type="date" name="exp_date[]" class="form-control"
                               value="{{ $detail->exp_date ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label>Stock Live</label>
                        <input type="number" name="stock_live[]" class="form-control"
                               value="{{ $detail->stock_live ?? '' }}" placeholder="e.g. 50">
                    </div>
                    <div class="col-md-6">
                        <label>Stock PO</label>
                        <input type="number" name="stock_po[]" class="form-control"
                               value="{{ $detail->stock_po ?? '' }}" placeholder="e.g. 20">
                    </div>
                    <div class="col-md-6">
                        <label>Min. Persediaan</label>
                        <input type="number" name="min_persediaan[]" class="form-control"
                               value="{{ $detail->min_persediaan ?? '' }}" placeholder="e.g. 10">
                    </div>
                    <div class="col-md-6">
                        <label>Harga Beli</label>
                        <input type="text"
                            name="harga_beli[]"
                            class="form-control format-rupiah"
                            value="{{ number_format($detail->harga_beli ?? 0, 0, ',', '.') }}"
                            placeholder="e.g. 500.000">
                    </div>
                    <div class="col-md-6">
                        <label>Kode Rak</label>
                        <input type="text" name="kode_rak[]" class="form-control"
                               value="{{ $detail->kode_rak ?? '' }}" placeholder="e.g. A12">
                    </div>
                </div>
            </td>
            <td class="align-middle text-center">
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
</style>
@endpush

@push('scripts')
{{-- Helper Data --}}
<script>
    // API URL untuk search produk
    window.produkApiUrl = "{{ route('api.produk.search') }}";

    // Opsi jenis untuk dynamic row
    window.jenisOptions = `
        <option value="" hidden>-- Pilih Jenis --</option>
        @foreach($jenis as $j)
            <option value="{{ $j->nama_jenis }}">{{ $j->nama_jenis }}</option>
        @endforeach
        <option value="Lainnya">Lainnya</option>
    `;

    // Opsi satuan untuk dynamic row
    window.satuanOptions = `
        <option value="" hidden>-- Pilih Satuan --</option>
        @foreach($satuans as $s)
            <option value="{{ $s->nama_satuan }}">{{ $s->nama_satuan }}</option>
        @endforeach
    `;
</script>

{{-- Format Rupiah --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rupiahInputs = document.querySelectorAll('.format-rupiah');

        rupiahInputs.forEach(input => {
            input.addEventListener('input', function (e) {
                let value = this.value.replace(/\D/g, '');
                this.value = new Intl.NumberFormat('id-ID').format(value);
            });

            input.form?.addEventListener('submit', function () {
                rupiahInputs.forEach(inp => {
                    inp.value = inp.value.replace(/\./g, '');
                });
            });
        });
    });
</script>

{{-- Main Script --}}
<script>
$(document).ready(function() {

    // --- Inisialisasi Select2 untuk Produk ---
    function initProdukSelect2(el) {
        const row = el.closest('.supplier-detail-item');
        const jenis = row.find('.jenis-select').val();
        
        // Jika jenis adalah "Lainnya", tidak perlu Select2
        if (jenis === 'Lainnya' || !jenis) {
            return;
        }
        
        const apiUrl = window.produkApiUrl;
        
        el.select2({
            placeholder: '-- Pilih atau ketik untuk mencari --',
            allowClear: true,
            width: '100%',
            ajax: {
                url: apiUrl,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        jenis: jenis, // Filter by jenis
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    const items = (data.items || []).map(item => {
                        return {
                            id: item.id,
                            text: item.text,
                            kode_produk: item.kode_produk,
                            nama_produk: item.nama_produk,
                            merk: item.merk || '',
                            satuan: item.satuan || '',
                            harga_beli: item.harga_beli || 0
                        };
                    });
                    
                    return {
                        results: items,
                        pagination: {
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });
    }

    // Inisialisasi Select2 pada semua element yang sudah ada
    $('.produk-select').each(function() {
        const row = $(this).closest('.supplier-detail-item');
        const jenis = row.find('.jenis-select').val();
        const produkSelect = $(this);
        const namaManual = row.find('.nama-manual');
        
        if (!jenis) {
            produkSelect.hide().prop('disabled', true).prop('required', false);
            namaManual.hide().prop('disabled', true).prop('required', false);
        } else if (jenis === 'Lainnya') {
            produkSelect.hide().prop('disabled', true).prop('required', false);
            namaManual.show().prop('disabled', false).prop('required', true);
        } else {
            produkSelect.show().prop('disabled', false).prop('required', true);
            namaManual.hide().prop('disabled', true).prop('required', false);
            initProdukSelect2(produkSelect);
        }
    });

    // --- Handle Perubahan Jenis ---
    $('#detail-table').on('change', '.jenis-select', function() {
        const row = $(this).closest('.supplier-detail-item');
        const jenis = $(this).val();
        const produkSelect = row.find('.produk-select');
        const namaManual = row.find('.nama-manual');

        // Reset field product_id
        row.find('.product-id-input').val('');
        
        if (produkSelect.hasClass('select2-hidden-accessible')) {
            produkSelect.val(null).trigger('change');
            produkSelect.select2('destroy');
        }
        produkSelect.val('');
        namaManual.val('');
        
        if (!jenis) {
            produkSelect.hide().prop('disabled', true).prop('required', false);
            produkSelect.html('<option value="">-- Pilih Jenis Terlebih Dahulu --</option>');
            namaManual.hide().prop('disabled', true).prop('required', false);
        } else if (jenis === 'Lainnya') {
            produkSelect.hide().prop('disabled', true).prop('required', false);
            namaManual.show().prop('disabled', false).prop('required', true);
            namaManual.focus();
        } else {
            produkSelect.show().prop('disabled', false).prop('required', true);
            produkSelect.html('<option value="">-- Pilih atau ketik untuk mencari --</option>');
            namaManual.hide().prop('disabled', true).prop('required', false);
            initProdukSelect2(produkSelect);
        }
    });

    // --- Handle Select2 Selection ---
    $('#detail-table').on('select2:select', '.produk-select', function(e) {
        const data = e.params.data;
        const row = $(this).closest('.supplier-detail-item');
        
        console.log('Selected product:', data);

        // Simpan product_id
        row.find('.product-id-input').val(data.id);
        
        // Auto-fill data produk
        if (data.merk) {
            row.find('.merk-input').val(data.merk);
        }
        if (data.satuan) {
            row.find('select[name="satuan[]"]').val(data.satuan);
        }
        if (data.harga_beli) {
            const formattedPrice = new Intl.NumberFormat('id-ID').format(data.harga_beli);
            row.find('input[name="harga_beli[]"]').val(formattedPrice);
        }
    });

    // Handle Select2 Clear
    $('#detail-table').on('select2:clear', '.produk-select', function(e) {
        const row = $(this).closest('.supplier-detail-item');
        row.find('.product-id-input').val('');
    });

    // Handle Input Manual
    $('#detail-table').on('input', '.nama-manual', function() {
        const row = $(this).closest('.supplier-detail-item');
        row.find('.product-id-input').val('');
    });

    // --- Tambah Baris Baru ---
    $('#detail-table').on('click', '.btn-add', function() {
        let $tableBody = $('#detail-table tbody');
        let jenisOptions = window.jenisOptions || '<option value="">-- Pilih Jenis --</option>';
        let satuanOptions = window.satuanOptions || '<option value="">-- Pilih Satuan --</option>';
        
        let $newRow = $('<tr class="supplier-detail-item" data-detail-id="">' +
            '<td>' +
                '<input type="hidden" name="detail_id[]" value="">' +
                '<input type="hidden" name="product_id[]" class="product-id-input" value="">' +
                '<div class="row g-2">' +
                    '<div class="col-md-6">' +
                        '<label>No Batch</label>' +
                        '<input type="text" name="no_batch[]" class="form-control" placeholder="e.g. BTC-36523">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Judul</label>' +
                        '<input type="text" name="judul[]" class="form-control" placeholder="e.g. Obat Sakit Kepala">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Jenis <span class="text-danger">*</span></label>' +
                        '<select name="jenis[]" class="form-select jenis-select" required>' +
                            jenisOptions +
                        '</select>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Nama Produk <span class="text-danger">*</span></label>' +
                        '<select class="form-control produk-select" style="display: none;" disabled>' +
                            '<option value="">-- Pilih Jenis Terlebih Dahulu --</option>' +
                        '</select>' +
                        '<input type="text" name="nama_manual[]" class="form-control nama-manual" style="display: none;" placeholder="Masukkan nama barang" disabled>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Merk</label>' +
                        '<input type="text" name="merk[]" class="form-control merk-input" placeholder="e.g. Kimia Farma">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Satuan <span class="text-danger">*</span></label>' +
                        '<select name="satuan[]" class="form-select" required>' +
                            satuanOptions +
                        '</select>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Exp Date</label>' +
                        '<input type="date" name="exp_date[]" class="form-control">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Stock Live</label>' +
                        '<input type="number" name="stock_live[]" class="form-control" placeholder="e.g. 50">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Stock PO</label>' +
                        '<input type="number" name="stock_po[]" class="form-control" placeholder="e.g. 20">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Min. Persediaan</label>' +
                        '<input type="number" name="min_persediaan[]" class="form-control" placeholder="e.g. 10">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Harga Beli</label>' +
                        '<input type="text" name="harga_beli[]" class="form-control format-rupiah" placeholder="e.g. 5.000">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Kode Rak</label>' +
                        '<input type="text" name="kode_rak[]" class="form-control" placeholder="e.g. A12">' +
                    '</div>' +
                '</div>' +
            '</td>' +
            '<td class="align-middle text-center">' +
                '<div class="d-flex flex-column justify-content-center align-items-center gap-2">' +
                    '<button type="button" class="btn btn-sm btn-outline-success btn-add" title="Tambah Baris">' +
                        '<i class="ri-add-line"></i>' +
                    '</button>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger btn-remove" title="Hapus Baris">' +
                        '<i class="ri-subtract-line"></i>' +
                    '</button>' +
                '</div>' +
            '</td>' +
        '</tr>');

        $tableBody.append($newRow);
        
        // Reinit format rupiah untuk row baru
        const newRupiahInput = $newRow.find('.format-rupiah');
        newRupiahInput.on('input', function() {
            let value = this.value.replace(/\D/g, '');
            this.value = new Intl.NumberFormat('id-ID').format(value);
        });
    });

    // --- Hapus Baris ---
    $('#detail-table').on('click', '.btn-remove', function() {
        const rowCount = $('#detail-table tbody tr').length;
        
        if (rowCount > 1) {
            let $row = $(this).closest('tr');
            let $select = $row.find('.produk-select');
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