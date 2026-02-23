<h5 class="mb-3 fw-semibold">Data Produk</h5>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Kode Produk</label>
        <input type="text"
               name="kode_produk"
               class="form-control bg-light"
               placeholder="Auto-generate"
               value="{{ old('kode_produk', $produk->kode_produk ?? '') }}"
               readonly>
        <small class="text-muted">Kode produk akan dibuat otomatis</small>
    </div>
    <div class="col-md-6">
        <label class="form-label">NIE (Nomor Izin Edar) <span class="text-danger">*</span></label>
        <input type="text"
               name="nie"
               class="form-control @error('nie') is-invalid @enderror"
               placeholder="e.g. DKL1234567890123"
               value="{{ old('nie', $produk->nie ?? '') }}"
               required>
        @error('nie')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
        <input type="text"
               name="nama_produk"
               class="form-control @error('nama_produk') is-invalid @enderror"
               placeholder="e.g. Paracetamol 500mg"
               value="{{ old('nama_produk', $produk->nama_produk ?? '') }}"
               required>
        @error('nama_produk')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Merk</label>
        <input type="text"
               name="merk"
               class="form-control @error('merk') is-invalid @enderror"
               placeholder="e.g. Kimia Farma"
               value="{{ old('merk', $produk->merk ?? '') }}">
        @error('merk')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Jenis <span class="text-danger">*</span></label>
        <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" id="select-jenis" required>
            <option value="" hidden>-- Pilih Jenis --</option>
            @foreach($jenis as $j)
                <option value="{{ $j->nama_jenis }}"
                    {{ old('jenis', $produk->jenis ?? '') == $j->nama_jenis ? 'selected' : '' }}>
                    {{ $j->nama_jenis }}
                </option>
            @endforeach
        </select>
        @error('jenis')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" id="select-status" required>
            <option value="" hidden>-- Pilih Status --</option>
            <option value="aktif"    {{ old('status', $produk->status ?? 'aktif') == 'aktif'    ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ old('status', $produk->status ?? '')       == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <label class="form-label">Deskripsi</label>
        <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                  name="deskripsi"
                  rows="3"
                  placeholder="Keterangan tambahan tentang produk">{{ old('deskripsi', $produk->deskripsi ?? '') }}</textarea>
        @error('deskripsi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<hr class="my-4">

{{-- ============================================================
     SECTION 2: Satuan Dasar & Harga Referensi
     ============================================================ --}}
<h5 class="mb-1 fw-semibold">Satuan Dasar & Harga Referensi</h5>
<p class="text-muted small mb-3">
    Satuan dasar adalah satuan terkecil produk (contoh: <strong>test</strong>, <strong>pcs</strong>).
    Harga dasar adalah harga jual per 1 satuan dasar yang dipakai sebagai acuan kalkulasi otomatis.
</p>

<div class="row mb-4">
    <div class="col-md-4">
        <label class="form-label">Satuan Dasar <span class="text-danger">*</span></label>
        <select name="satuan_dasar_id"
                id="select-satuan-dasar"
                class="form-select @error('satuan_dasar_id') is-invalid @enderror"
                required>
            <option value="" hidden>-- Pilih Satuan Dasar --</option>
            @foreach($satuan as $item)
                <option value="{{ $item->id }}"
                    {{ old('satuan_dasar_id', $produk->satuan_dasar_id ?? '') == $item->id ? 'selected' : '' }}>
                    {{ $item->nama_satuan }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Satuan terkecil (misal: test, pcs)</small>
        @error('satuan_dasar_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Harga Beli per Satuan Dasar <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="text"
                   name="harga_beli"
                   id="harga_beli_dasar"
                   class="form-control format-rupiah @error('harga_beli') is-invalid @enderror"
                   value="{{ old('harga_beli', isset($produk) ? number_format($produk->harga_beli, 0, ',', '.') : '') }}"
                   placeholder="0"
                   required>
            @error('harga_beli')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label">Harga Dasar / Jual per Satuan Dasar <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="text"
                   name="harga_dasar"
                   id="harga_dasar"
                   class="form-control format-rupiah @error('harga_dasar') is-invalid @enderror"
                   value="{{ old('harga_dasar', isset($produk) ? number_format($produk->harga_dasar, 0, ',', '.') : '') }}"
                   placeholder="0"
                   required>
            @error('harga_dasar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="text-muted">Acuan kalkulasi otomatis: harga ini x isi satuan</small>
    </div>
</div>

<hr class="my-4">

{{-- ============================================================
     SECTION 3: Satuan Jual (Dynamic rows)
     ============================================================ --}}
<div class="d-flex justify-content-between align-items-center mb-1">
    <div>
        <h5 class="mb-0 fw-semibold">Satuan Jual</h5>
        <p class="text-muted small mb-0">
            Definisikan semua satuan yang bisa dijual.
            Centang <strong>Otomatis</strong> agar harga dihitung dari harga dasar × isi — atau uncheck untuk isi manual.
        </p>
    </div>
    <button type="button" class="btn btn-success btn-sm ms-3" id="btn-tambah-satuan">
        <i class="bi bi-plus-lg me-1"></i> Tambah Satuan
    </button>
</div>

<div class="alert alert-info py-2 px-3 small mt-2 mb-3">
    <i class="bi bi-info-circle me-1"></i>
    <strong>Contoh:</strong>
    Reagen CPRR — satuan dasar: <em>test</em>, harga dasar: Rp 5.000.
    Buat baris <em>Galon</em> dengan isi <strong>500</strong> → harga otomatis = Rp 2.500.000.
    Buat baris <em>Test</em> dengan isi <strong>1</strong> → harga otomatis = Rp 5.000.
</div>

<div class="table-responsive">
    <table class="table table-bordered align-middle" id="tabel-satuan-jual">
        <thead class="table-light text-center">
            <tr>
                <th style="width:5%">Default</th>
                <th style="width:17%">Satuan</th>
                <th style="width:16%">Label Tampil</th>
                <th style="width:10%">Isi</th>
                <th style="width:6%">Otomatis</th>
                <th style="width:17%">Harga Beli</th>
                <th style="width:17%">Harga Jual</th>
                <th style="width:5%">Aksi</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th><small class="text-muted fw-normal">qty satuan dasar</small></th>
                <th></th>
                <th><small class="text-muted fw-normal" id="preview-harga-beli-label">x harga beli</small></th>
                <th><small class="text-muted fw-normal" id="preview-harga-dasar-label">x harga dasar</small></th>
                <th></th>
            </tr>
        </thead>
        <tbody id="satuan-jual-body">

            @php
                // Ambil data lama dari old() jika ada (setelah validation failed)
                $existingSatuanJual = old('satuan_jual', isset($produk) ? $produk->produkSatuans->toArray() : null);
            @endphp

            @if($existingSatuanJual && count($existingSatuanJual))
                @foreach($existingSatuanJual as $i => $ps)
                <tr class="satuan-row">
                    <td class="text-center">
                        <input type="radio"
                               name="satuan_jual_default"
                               value="{{ $i }}"
                               class="form-check-input radio-default"
                               {{ ($ps['is_default'] ?? false) ? 'checked' : '' }}>
                        <input type="hidden"
                               name="satuan_jual[{{ $i }}][is_default]"
                               value="{{ ($ps['is_default'] ?? false) ? 1 : 0 }}"
                               class="hidden-default">
                    </td>
                    <td>
                        <select name="satuan_jual[{{ $i }}][satuan_id]"
                                class="form-select form-select-sm satuan-select @error('satuan_jual.'.$i.'.satuan_id') is-invalid @enderror"
                                required>
                            <option value="" hidden>-- Pilih --</option>
                            @foreach($satuan as $item)
                                <option value="{{ $item->id }}"
                                    {{ ($ps['satuan_id'] ?? '') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_satuan }}
                                </option>
                            @endforeach
                        </select>
                        @error('satuan_jual.'.$i.'.satuan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="text"
                               name="satuan_jual[{{ $i }}][label]"
                               class="form-control form-control-sm @error('satuan_jual.'.$i.'.label') is-invalid @enderror"
                               value="{{ $ps['label'] ?? '' }}"
                               placeholder="e.g. Galon"
                               required>
                        @error('satuan_jual.'.$i.'.label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="number"
                               name="satuan_jual[{{ $i }}][isi]"
                               class="form-control form-control-sm isi-input @error('satuan_jual.'.$i.'.isi') is-invalid @enderror"
                               value="{{ $ps['isi'] ?? 1 }}"
                               min="1"
                               step="any"
                               required>
                        @error('satuan_jual.'.$i.'.isi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </td>
                    <td class="text-center">
                        <input type="checkbox"
                               name="satuan_jual[{{ $i }}][harga_otomatis]"
                               class="form-check-input cb-otomatis"
                               value="1"
                               {{ ($ps['harga_otomatis'] ?? true) ? 'checked' : '' }}>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text"
                                   name="satuan_jual[{{ $i }}][harga_beli]"
                                   class="form-control format-rupiah harga-beli-field @error('satuan_jual.'.$i.'.harga_beli') is-invalid @enderror"
                                   value="{{ isset($ps['harga_beli']) ? number_format((float)$ps['harga_beli'], 0, ',', '.') : '' }}"
                                   placeholder="0"
                                   {{ ($ps['harga_otomatis'] ?? true) ? 'readonly' : '' }}>
                        </div>
                        <small class="text-muted preview-beli-calc"></small>
                        @error('satuan_jual.'.$i.'.harga_beli')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text"
                                   name="satuan_jual[{{ $i }}][harga_jual]"
                                   class="form-control format-rupiah harga-jual-field @error('satuan_jual.'.$i.'.harga_jual') is-invalid @enderror"
                                   value="{{ isset($ps['harga_jual']) ? number_format((float)$ps['harga_jual'], 0, ',', '.') : '' }}"
                                   placeholder="0"
                                   {{ ($ps['harga_otomatis'] ?? true) ? 'readonly' : '' }}>
                        </div>
                        <small class="text-muted preview-jual-calc"></small>
                        @error('satuan_jual.'.$i.'.harga_jual')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-hapus-satuan" title="Hapus baris">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            @else
                {{-- Baris default kosong untuk halaman create --}}
                <tr class="satuan-row">
                    <td class="text-center">
                        <input type="radio" name="satuan_jual_default" value="0"
                               class="form-check-input radio-default" checked>
                        <input type="hidden" name="satuan_jual[0][is_default]" value="1" class="hidden-default">
                    </td>
                    <td>
                        <select name="satuan_jual[0][satuan_id]"
                                class="form-select form-select-sm satuan-select"
                                required>
                            <option value="" hidden>-- Pilih --</option>
                            @foreach($satuan as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_satuan }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" name="satuan_jual[0][label]"
                               class="form-control form-control-sm"
                               placeholder="e.g. Test" required>
                    </td>
                    <td>
                        <input type="number" name="satuan_jual[0][isi]"
                               class="form-control form-control-sm isi-input"
                               value="1" min="0.0001" step="any" required>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" name="satuan_jual[0][harga_otomatis]"
                               class="form-check-input cb-otomatis" value="1" checked>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="satuan_jual[0][harga_beli]"
                                   class="form-control format-rupiah harga-beli-field"
                                   placeholder="0" readonly>
                        </div>
                        <small class="text-muted preview-beli-calc"></small>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="satuan_jual[0][harga_jual]"
                                   class="form-control format-rupiah harga-jual-field"
                                   placeholder="0" readonly>
                        </div>
                        <small class="text-muted preview-jual-calc"></small>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-hapus-satuan" disabled title="Hapus baris">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @endif

        </tbody>
    </table>
</div>

@error('satuan_jual')
    <div class="alert alert-danger py-2 small">{{ $message }}</div>
@enderror

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const body                = document.getElementById('satuan-jual-body');
    const btnTambah           = document.getElementById('btn-tambah-satuan');
    const hargaBeliDasarInput = document.getElementById('harga_beli_dasar');
    const hargaDasarInput     = document.getElementById('harga_dasar');

    // ── Bangun options satuan sekali saja ──
    const satuanOpts = `
        @foreach($satuan as $item)
            <option value="{{ $item->id }}">{{ $item->nama_satuan }}</option>
        @endforeach
    `;

    // ─────────────────────────────────────────────
    // SELECT2: inisialisasi satu elemen <select>
    // ─────────────────────────────────────────────
    function initSelect2(selectEl) {
        $(selectEl).select2({
            theme       : 'bootstrap-5',
            placeholder : '-- Pilih --',
            allowClear  : true,
            width       : '100%',
        });
    }

    // ─────────────────────────────────────────────
    // SELECT2: inisialisasi select di luar tabel
    // ─────────────────────────────────────────────
    initSelect2(document.getElementById('select-jenis'));
    initSelect2(document.getElementById('select-status'));
    initSelect2(document.getElementById('select-satuan-dasar'));

    // ─────────────────────────────────────────────
    // HELPER: parse angka dari format rupiah
    // ─────────────────────────────────────────────
    function parseRupiah(str) {
        return parseFloat((str + '').replace(/\./g, '').replace(',', '.')) || 0;
    }

    function formatRupiah(val) {
        if (!val && val !== 0) return '';
        return new Intl.NumberFormat('id-ID').format(Math.round(val));
    }

    // ─────────────────────────────────────────────
    // HELPER: update preview & nilai harga otomatis
    // ─────────────────────────────────────────────
    function updatePreview(row) {
        const cb        = row.querySelector('.cb-otomatis');
        const isiInput  = row.querySelector('.isi-input');
        const beliFld   = row.querySelector('.harga-beli-field');
        const jualFld   = row.querySelector('.harga-jual-field');
        const prevBeli  = row.querySelector('.preview-beli-calc');
        const prevJual  = row.querySelector('.preview-jual-calc');

        const isi        = parseFloat(isiInput.value) || 0;
        const hargaBeli  = parseRupiah(hargaBeliDasarInput.value);
        const hargaDasar = parseRupiah(hargaDasarInput.value);

        if (cb.checked) {
            const nilaiBeliAuto = hargaBeli * isi;
            const nilaiJualAuto = hargaDasar * isi;

            beliFld.value = formatRupiah(nilaiBeliAuto);
            jualFld.value = formatRupiah(nilaiJualAuto);

            if (prevBeli) prevBeli.textContent = isi > 0
                ? `${formatRupiah(hargaBeli)} × ${isi} = Rp ${formatRupiah(nilaiBeliAuto)}`
                : '';
            if (prevJual) prevJual.textContent = isi > 0
                ? `${formatRupiah(hargaDasar)} × ${isi} = Rp ${formatRupiah(nilaiJualAuto)}`
                : '';
        } else {
            if (prevBeli) prevBeli.textContent = '';
            if (prevJual) prevJual.textContent = '';
        }
    }

    function updateAllPreviews() {
        body.querySelectorAll('.satuan-row').forEach(updatePreview);
    }

    // ─────────────────────────────────────────────
    // FORMAT RUPIAH pada input
    // ─────────────────────────────────────────────
    function bindFormatRupiah(input) {
        input.addEventListener('input', function () {
            const raw = this.value.replace(/\D/g, '');
            this.value = new Intl.NumberFormat('id-ID').format(raw);
        });
    }

    // ─────────────────────────────────────────────
    // BIND semua event per baris
    // ─────────────────────────────────────────────
    function bindRow(tr) {
        const cb       = tr.querySelector('.cb-otomatis');
        const isiInput = tr.querySelector('.isi-input');
        const beliFld  = tr.querySelector('.harga-beli-field');
        const jualFld  = tr.querySelector('.harga-jual-field');
        const radio    = tr.querySelector('.radio-default');
        const selectEl = tr.querySelector('.satuan-select');

        // Inisialisasi Select2 untuk select satuan di baris ini
        initSelect2(selectEl);

        // Toggle readonly
        cb.addEventListener('change', function () {
            beliFld.readOnly = this.checked;
            jualFld.readOnly = this.checked;
            if (this.checked) {
                updatePreview(tr);
            } else {
                tr.querySelector('.preview-beli-calc').textContent = '';
                tr.querySelector('.preview-jual-calc').textContent = '';
            }
        });

        // Update preview saat isi berubah
        isiInput.addEventListener('input', () => updatePreview(tr));

        // Format rupiah pada harga manual
        [beliFld, jualFld].forEach(bindFormatRupiah);

        // Radio default
        radio.addEventListener('change', syncHiddenDefault);

        // Preview awal
        updatePreview(tr);
    }

    // ─────────────────────────────────────────────
    // SYNC hidden is_default dari radio
    // ─────────────────────────────────────────────
    function syncHiddenDefault() {
        body.querySelectorAll('.satuan-row').forEach(row => {
            const radio  = row.querySelector('.radio-default');
            const hidden = row.querySelector('.hidden-default');
            if (hidden) hidden.value = radio.checked ? 1 : 0;
        });
    }

    // ─────────────────────────────────────────────
    // HAPUS BARIS
    // ─────────────────────────────────────────────
    function bindHapus() {
        body.querySelectorAll('.btn-hapus-satuan').forEach(btn => {
            btn.onclick = function () {
                const row        = this.closest('tr');
                const wasDefault = row.querySelector('.radio-default').checked;

                // Destroy Select2 sebelum remove agar tidak memory leak
                const sel = row.querySelector('.satuan-select');
                if (sel && $(sel).data('select2')) {
                    $(sel).select2('destroy');
                }

                row.remove();
                reindexRows();
                updateHapusBtn();

                // Jika yang dihapus adalah default, set baris pertama jadi default
                if (wasDefault) {
                    const firstRow = body.querySelector('.satuan-row');
                    if (firstRow) {
                        firstRow.querySelector('.radio-default').checked = true;
                        syncHiddenDefault();
                    }
                }
            };
        });
    }

    // ─────────────────────────────────────────────
    // RE-INDEX name[] setelah hapus baris
    // ─────────────────────────────────────────────
    function reindexRows() {
        body.querySelectorAll('.satuan-row').forEach((row, i) => {
            row.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/\[\d+\]/, `[${i}]`);
            });
            const radio = row.querySelector('.radio-default');
            if (radio) radio.value = i;
        });
    }

    // ─────────────────────────────────────────────
    // DISABLE tombol hapus jika hanya 1 baris
    // ─────────────────────────────────────────────
    function updateHapusBtn() {
        const rows = body.querySelectorAll('.satuan-row');
        rows.forEach(row => {
            row.querySelector('.btn-hapus-satuan').disabled = rows.length === 1;
        });
    }

    // ─────────────────────────────────────────────
    // TAMBAH BARIS BARU
    // ─────────────────────────────────────────────
    btnTambah.addEventListener('click', function () {
        const idx = body.querySelectorAll('.satuan-row').length;
        const tr  = document.createElement('tr');
        tr.className = 'satuan-row';
        tr.innerHTML = `
            <td class="text-center">
                <input type="radio" name="satuan_jual_default" value="${idx}"
                       class="form-check-input radio-default">
                <input type="hidden" name="satuan_jual[${idx}][is_default]"
                       value="0" class="hidden-default">
            </td>
            <td>
                <select name="satuan_jual[${idx}][satuan_id]"
                        class="form-select form-select-sm satuan-select" required>
                    <option value="" hidden>-- Pilih --</option>
                    ${satuanOpts}
                </select>
            </td>
            <td>
                <input type="text" name="satuan_jual[${idx}][label]"
                       class="form-control form-control-sm"
                       placeholder="e.g. Box" required>
            </td>
            <td>
                <input type="number" name="satuan_jual[${idx}][isi]"
                       class="form-control form-control-sm isi-input"
                       value="1" min="0.0001" step="any" required>
            </td>
            <td class="text-center">
                <input type="checkbox" name="satuan_jual[${idx}][harga_otomatis]"
                       class="form-check-input cb-otomatis" value="1" checked>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="text" name="satuan_jual[${idx}][harga_beli]"
                           class="form-control format-rupiah harga-beli-field"
                           placeholder="0" readonly>
                </div>
                <small class="text-muted preview-beli-calc"></small>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="text" name="satuan_jual[${idx}][harga_jual]"
                           class="form-control format-rupiah harga-jual-field"
                           placeholder="0" readonly>
                </div>
                <small class="text-muted preview-jual-calc"></small>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger btn-hapus-satuan" title="Hapus baris">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        body.appendChild(tr);
        bindRow(tr);
        bindHapus();
        updateHapusBtn();
    });

    // ─────────────────────────────────────────────
    // FORMAT RUPIAH untuk input harga dasar (header)
    // ─────────────────────────────────────────────
    [hargaBeliDasarInput, hargaDasarInput].forEach(inp => {
        bindFormatRupiah(inp);
        inp.addEventListener('input', updateAllPreviews);
    });

    // ─────────────────────────────────────────────
    // BERSIHKAN format rupiah sebelum submit
    // ─────────────────────────────────────────────
    const form = document.getElementById('form-produk');
    if (form) {
        form.addEventListener('submit', function () {
            document.querySelectorAll('.format-rupiah').forEach(inp => {
                inp.value = inp.value.replace(/\./g, '').replace(',', '.');
            });
        });
    }

    // ─────────────────────────────────────────────
    // INIT: bind semua baris yang sudah ada
    // ─────────────────────────────────────────────
    body.querySelectorAll('.satuan-row').forEach(bindRow);
    bindHapus();
    updateHapusBtn();
    syncHiddenDefault();
});
</script>
@endpush