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


<div class="d-flex justify-content-between align-items-center mb-1">
    <div>
        <h5 class="mb-0 fw-semibold">Satuan Produk</h5>
        {{-- ← PERBAIKAN: penjelasan diperbarui agar sesuai logika konversi --}}
        <p class="text-muted small mb-0">
            Tentukan semua satuan produk beserta nilai konversinya ke satuan dasar (PCS/unit terkecil).
            <br>Baris yang ditandai <strong>Default</strong> adalah satuan dasar — konversinya otomatis <strong>1</strong>.
        </p>
    </div>
    <button type="button" class="btn btn-success btn-sm ms-3" id="btn-tambah-satuan">
        <i class="bi bi-plus-lg me-1"></i> Tambah Satuan
    </button>
</div>

<div class="table-responsive mt-3">
    {{-- ← UPDATE: tambah kolom Kode Barcode --}}
    <table class="table table-bordered align-middle" id="tabel-satuan-jual">
        <thead class="table-light text-center">
            <tr>
                <th style="width:7%">Default</th>
                <th style="width:28%">Satuan</th>
                <th style="width:28%">Konversi ke PCS</th>
                <th style="width:27%">Kode Barcode</th>
                <th style="width:10%">Aksi</th>
            </tr>
        </thead>
        <tbody id="satuan-jual-body">

            @php
                $existingSatuanJual = old('satuan_jual',
                    isset($produk)
                        ? $produk->produkSatuans->map(fn($ps) => [
                            'satuan_id'    => $ps->satuan_id,
                            'konversi'     => $ps->konversi,
                            'is_default'   => $ps->is_default,
                            'kode_barcode' => $ps->kode_barcode, // ← TAMBAH
                        ])->toArray()
                        : null
                );
            @endphp

            @if($existingSatuanJual && count($existingSatuanJual))
                @foreach($existingSatuanJual as $i => $ps)
                @php $isDefault = !empty($ps['is_default']); @endphp
                <tr class="satuan-row">
                    <td class="text-center">
                        <input type="radio"
                               name="satuan_jual_default"
                               value="{{ $i }}"
                               class="form-check-input radio-default"
                               {{ $isDefault ? 'checked' : '' }}>
                        <input type="hidden"
                               name="satuan_jual[{{ $i }}][is_default]"
                               value="{{ $isDefault ? 1 : 0 }}"
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
                        <div class="input-group input-group-sm">
                            <input type="number"
                                   name="satuan_jual[{{ $i }}][konversi]"
                                   class="form-control form-control-sm konversi-input @error('satuan_jual.'.$i.'.konversi') is-invalid @enderror"
                                   value="{{ $ps['konversi'] ?? 1 }}"
                                   min="1"
                                   {{ $isDefault ? 'readonly' : 'required' }}>
                            <span class="input-group-text">PCS</span>
                        </div>
                        @if($isDefault)
                            <small class="text-muted">Satuan dasar, konversi selalu 1</small>
                        @endif
                        @error('satuan_jual.'.$i.'.konversi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    {{-- ← TAMBAH: kolom kode barcode per kemasan --}}
                    <td>
                        <input type="text"
                               name="satuan_jual[{{ $i }}][kode_barcode]"
                               class="form-control form-control-sm @error('satuan_jual.'.$i.'.kode_barcode') is-invalid @enderror"
                               value="{{ $ps['kode_barcode'] ?? '' }}"
                               placeholder="Scan / ketik barcode">
                        @error('satuan_jual.'.$i.'.kode_barcode')
                            <div class="invalid-feedback">{{ $message }}</div>
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
                {{-- Baris default pertama: satuan dasar --}}
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
                        <div class="input-group input-group-sm">
                            <input type="number"
                                   name="satuan_jual[0][konversi]"
                                   class="form-control form-control-sm konversi-input"
                                   value="1"
                                   min="1"
                                   readonly>
                            <span class="input-group-text">PCS</span>
                        </div>
                        <small class="text-muted">Satuan dasar, konversi selalu 1</small>
                    </td>
                    {{-- ← TAMBAH --}}
                    <td>
                        <input type="text"
                               name="satuan_jual[0][kode_barcode]"
                               class="form-control form-control-sm"
                               placeholder="Scan / ketik barcode">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-hapus-satuan"
                                disabled title="Hapus baris">
                            <i class="ri ri-delete-bin-line"></i>
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

    const body      = document.getElementById('satuan-jual-body');
    const btnTambah = document.getElementById('btn-tambah-satuan');

    const satuanOpts = `
        @foreach($satuan as $item)
            <option value="{{ $item->id }}">{{ $item->nama_satuan }}</option>
        @endforeach
    `;

    // ── Select2 ──────────────────────────────────────────
    function initSelect2(el) {
        $(el).select2({
            theme      : 'bootstrap-5',
            placeholder: '-- Pilih --',
            allowClear : true,
            width      : '100%',
        });
    }

    initSelect2(document.getElementById('select-jenis'));
    initSelect2(document.getElementById('select-status'));

    // ← HAPUS: initSelect2 untuk select-satuan-dasar (sudah tidak ada)

    // ── Sync hidden is_default dari radio ────────────────
    function syncHiddenDefault() {
        body.querySelectorAll('.satuan-row').forEach(row => {
            const radio  = row.querySelector('.radio-default');
            const hidden = row.querySelector('.hidden-default');
            if (hidden) hidden.value = radio.checked ? 1 : 0;
        });
    }

    // ── Konversi: jika baris = default, set konversi=1 & readonly ──
    // ← PERBAIKAN: logika baru untuk handle field konversi
    function updateKonversiState() {
        body.querySelectorAll('.satuan-row').forEach(row => {
            const radio    = row.querySelector('.radio-default');
            const konversi = row.querySelector('.konversi-input');
            const hint     = row.querySelector('.konversi-hint');

            if (!konversi) return;

            if (radio.checked) {
                konversi.value    = 1;
                konversi.readOnly = true;
                if (hint) hint.style.display = 'block';
            } else {
                konversi.readOnly = false;
                if (hint) hint.style.display = 'none';
                // Jika konversi masih 1 setelah unset default, reset ke kosong agar user isi ulang
                if (parseInt(konversi.value) <= 1) konversi.value = '';
            }
        });
    }

    // ── Re-index name[] setelah hapus baris ──────────────
    function reindexRows() {
        body.querySelectorAll('.satuan-row').forEach((row, i) => {
            row.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/\[\d+\]/, `[${i}]`);
            });
            const radio = row.querySelector('.radio-default');
            if (radio) radio.value = i;
        });
    }

    // ── Disable tombol hapus jika hanya 1 baris ──────────
    function updateHapusBtn() {
        const rows = body.querySelectorAll('.satuan-row');
        rows.forEach(row => {
            row.querySelector('.btn-hapus-satuan').disabled = rows.length === 1;
        });
    }

    // ── Bind hapus ───────────────────────────────────────
    function bindHapus() {
        body.querySelectorAll('.btn-hapus-satuan').forEach(btn => {
            btn.onclick = function () {
                const row        = this.closest('tr');
                const wasDefault = row.querySelector('.radio-default').checked;

                const sel = row.querySelector('.satuan-select');
                if (sel && $(sel).data('select2')) $(sel).select2('destroy');

                row.remove();
                reindexRows();
                updateHapusBtn();

                if (wasDefault) {
                    const firstRow = body.querySelector('.satuan-row');
                    if (firstRow) {
                        firstRow.querySelector('.radio-default').checked = true;
                        syncHiddenDefault();
                        updateKonversiState(); // ← PERBAIKAN: update state konversi setelah ganti default
                    }
                }
            };
        });
    }

    // ── Bind per baris ───────────────────────────────────
    function bindRow(tr) {
        const radio    = tr.querySelector('.radio-default');
        const selectEl = tr.querySelector('.satuan-select');

        initSelect2(selectEl);

        radio.addEventListener('change', function () {
            syncHiddenDefault();
            updateKonversiState(); // ← PERBAIKAN: update konversi saat ganti default
        });
    }

    // ── Tambah baris baru ─────────────────────────────────
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
                <div class="input-group input-group-sm">
                    <input type="number"
                           name="satuan_jual[${idx}][konversi]"
                           class="form-control form-control-sm konversi-input"
                           placeholder="e.g. 50"
                           min="1"
                           required>
                    <span class="input-group-text">PCS</span>
                </div>
                <small class="text-muted konversi-hint" style="display:none">Satuan dasar, konversi selalu 1</small>
            </td>
            <td>
                <input type="text"
                       name="satuan_jual[${idx}][kode_barcode]"
                       class="form-control form-control-sm"
                       placeholder="Scan / ketik barcode">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger btn-hapus-satuan" title="Hapus baris">
                    <i class="ri ri-delete-bin-line"></i>
                </button>
            </td>
        `;
        body.appendChild(tr);
        bindRow(tr);
        bindHapus();
        updateHapusBtn();
    });

    // ── Init semua baris yang sudah ada ──────────────────
    body.querySelectorAll('.satuan-row').forEach(bindRow);
    bindHapus();
    updateHapusBtn();
    syncHiddenDefault();
    updateKonversiState(); // ← PERBAIKAN: pastikan state konversi benar saat page load
});
</script>
@endpush