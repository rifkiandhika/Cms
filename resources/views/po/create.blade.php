@extends('layouts.app')

@section('title', 'Buat Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('po.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Buat PO {{ ucfirst($type) }}</li>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection { min-height: 38px; }
    .select2-container { width: 100% !important; }
    .batch-info { font-size: 0.75rem; color: #6c757d; margin-top: 0.25rem; }
    .stock-badge { font-size: 0.7rem; padding: 0.2rem 0.5rem; }
    .item-row { animation: fadeIn 0.3s; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="app-body">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('po.store') }}" method="POST" id="formPO">
        @csrf
        <input type="hidden" name="tipe_po" value="{{ $type }}">
        <input type="hidden" name="unit_pemohon" value="gudang">
        <input type="hidden" name="id_unit_pemohon" value="{{ auth()->user()->id_karyawan ?? '' }}">

        <div class="row">
            <!-- ── Kolom Kiri ── -->
            <div class="col-xl-8">

                <!-- Informasi Umum -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-information-line me-2"></i>Informasi Purchase Order
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            @if($type === 'penjualan')
                                <strong>PO Penjualan:</strong> Penjualan barang dari <strong>Gudang</strong> ke <strong>Customer</strong><br>
                                <small class="text-muted">
                                    <i class="ri-checkbox-circle-line me-1"></i> Memerlukan persetujuan Kepala Gudang dan Kasir<br>
                                    <i class="ri-checkbox-circle-line me-1"></i> Stok akan dikurangi dari Gudang setelah pengiriman
                                </small>
                            @else
                                <strong>PO Pembelian:</strong> Pembelian barang dari <strong>Supplier</strong> ke <strong>Gudang</strong><br>
                                <small class="text-muted">
                                    <i class="ri-checkbox-circle-line me-1"></i> Memerlukan persetujuan Kepala Gudang dan Kasir<br>
                                    <i class="ri-checkbox-circle-line me-1"></i> Stok akan ditambah ke Gudang setelah diterima
                                </small>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-user-line me-1"></i> Pemohon
                                </label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-building-line me-1"></i> Unit Pemohon
                                </label>
                                <input type="text" class="form-control" value="Gudang" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                @if($type === 'penjualan')
                                    <label class="form-label fw-semibold">
                                        <i class="ri-user-line me-1"></i> Customer <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('id_customer') is-invalid @enderror"
                                            name="id_customer" id="relasi" required>
                                        <option value="">-- Pilih Customer --</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ old('id_customer') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->nama_customer }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_customer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @else
                                    <label class="form-label fw-semibold">
                                        <i class="ri-store-line me-1"></i> Supplier <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('id_supplier') is-invalid @enderror"
                                            name="id_supplier" id="relasi" required>
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                {{ old('id_supplier') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->nama_supplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_supplier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-percent-line me-1"></i> Pajak (%)
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="pajak_persen"
                                           id="pajak_persen" value="{{ old('pajak_persen', 0) }}"
                                           min="0" max="100" step="0.01" placeholder="0">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Nilai pajak: <span id="nilai_pajak_display">Rp 0</span></small>
                                <input type="hidden" name="pajak" id="pajak_value">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="ri-chat-3-line me-1"></i> Catatan
                            </label>
                            <textarea class="form-control @error('catatan_pemohon') is-invalid @enderror"
                                      name="catatan_pemohon" rows="3"
                                      placeholder="Tambahkan catatan untuk PO ini...">{{ old('catatan_pemohon') }}</textarea>
                            @error('catatan_pemohon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Item PO -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="ri-shopping-cart-line me-2"></i>Item Purchase Order
                            </h5>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">
                                <i class="ri-add-line me-1"></i>Tambah Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="itemTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Produk <span class="text-danger">*</span></th>
                                        @if($type === 'penjualan')
                                        {{-- Kolom Satuan hanya untuk penjualan --}}
                                        <th width="160">Satuan <span class="text-danger">*</span></th>
                                        @endif
                                        <th width="130">Harga</th>
                                        <th width="90">Qty <span class="text-danger">*</span></th>
                                        <th width="140" class="text-end">Subtotal</th>
                                        <th width="60" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemTableBody">
                                    {{-- Rows ditambah via JS --}}
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="{{ $type === 'penjualan' ? 5 : 4 }}" class="text-end">Total:</th>
                                        <th class="text-end" id="totalHarga">Rp 0</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="{{ $type === 'penjualan' ? 5 : 4 }}" class="text-end">Pajak:</th>
                                        <th class="text-end" id="totalPajak">Rp 0</th>
                                        <th></th>
                                    </tr>
                                    <tr class="table-primary">
                                        <th colspan="{{ $type === 'penjualan' ? 5 : 4 }}" class="text-end">Grand Total:</th>
                                        <th class="text-end" id="grandTotal">Rp 0</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── Kolom Kanan: Ringkasan ── -->
            <div class="col-xl-4">
                <div class="card shadow-sm border-0 mb-4 position-sticky" style="top: 20px;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="ri-calculator-line me-2"></i>Ringkasan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Item:</span>
                            <strong id="summaryItemCount">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Quantity:</span>
                            <strong id="summaryTotalQty">0</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <strong id="summarySubtotal">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Pajak:</span>
                            <strong id="summaryPajak">Rp 0</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Grand Total:</span>
                            <h5 class="text-success mb-0" id="summaryGrandTotal">Rp 0</h5>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="ri-save-line me-1"></i> Simpan PO
                            </button>
                            <a href="{{ route('po.index') }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="ri-information-line text-info me-2"></i>Informasi
                        </h6>
                        <ul class="small mb-0">
                            <li class="mb-2">Pastikan semua item yang dipilih sudah benar</li>
                            @if($type === 'penjualan')
                                <li class="mb-2">Pilih <strong>Satuan</strong> setelah memilih produk — harga menyesuaikan otomatis</li>
                                <li class="mb-2 text-success"><strong>Contoh:</strong> Box (500 pcs) = Rp 2.500.000 | Pcs = Rp 5.000 | Test = Rp 5.000</li>
                                <li class="mb-2">Stok akan dikurangi setelah pengiriman disetujui</li>
                            @else
                                <li class="mb-2"><strong class="text-primary">Harga otomatis dari Detail Supplier</strong></li>
                                <li class="mb-2">Stok akan ditambah setelah barang diterima</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let itemCounter = 0;
const produkData  = Object.values(@json($produkList));
const isPenjualan = {{ $type === 'penjualan' ? 'true' : 'false' }};

// ─────────────────────────────────────────────────────────
// FILTER PRODUK (pembelian: filter by supplier)
// ─────────────────────────────────────────────────────────
@if($type === 'pembelian')
const relasiSelect = document.getElementById('relasi');
let filteredProduk = [];

relasiSelect.addEventListener('change', function () {
    const supplierId = this.value;
    filteredProduk = supplierId
        ? produkData.filter(p => p.supplier_id === supplierId)
        : [];

    document.getElementById('itemTableBody').innerHTML = '';
    itemCounter = 0;
    calculateTotal();
});
@else
// Penjualan: semua produk dari gudang tersedia
let filteredProduk = produkData;
@endif

// ─────────────────────────────────────────────────────────
// TAMBAH BARIS ITEM
// ─────────────────────────────────────────────────────────
function addItem() {
    const relasiValue = document.getElementById('relasi').value;

    if (!relasiValue) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: isPenjualan ? 'Pilih customer terlebih dahulu' : 'Pilih supplier terlebih dahulu'
        });
        return;
    }

    if (filteredProduk.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada Produk',
            text: isPenjualan ? 'Gudang tidak memiliki stok' : 'Supplier ini belum memiliki produk'
        });
        return;
    }

    itemCounter++;
    const tbody = document.getElementById('itemTableBody');
    const row   = document.createElement('tr');
    row.className = 'item-row';
    row.id = `item-${itemCounter}`;

    // ── HTML berbeda untuk penjualan vs pembelian ──
    const satuanColumn = isPenjualan ? `
        <td>
            <select class="form-select form-select-sm"
                    id="satuan-${itemCounter}"
                    name="items[${itemCounter}][id_produk_satuan]"
                    onchange="updatePriceFromSatuan(${itemCounter})"
                    disabled>
                <option value="">-- Pilih Satuan --</option>
            </select>
            <small class="text-muted" id="satuan-info-${itemCounter}"></small>
        </td>
    ` : `<input type="hidden" name="items[${itemCounter}][jenis]" id="jenis-${itemCounter}">`;

    row.innerHTML = `
        <td class="text-center">${itemCounter}</td>
        <td>
            <select class="form-select form-select-sm select2-produk"
                    name="items[${itemCounter}][id_produk]"
                    id="produk-${itemCounter}"
                    onchange="onProdukChange(${itemCounter})" required>
                <option value="">-- Pilih Produk --</option>
            </select>
            <div class="batch-info" id="batch-info-${itemCounter}"></div>
        </td>
        ${satuanColumn}
        <td>
            <input type="text" class="form-control form-control-sm text-end bg-light"
                   id="harga-${itemCounter}" readonly value="0">
            <input type="hidden" name="items[${itemCounter}][harga]"
                   id="harga-val-${itemCounter}" value="0">
            <small class="text-muted" id="harga-info-${itemCounter}"></small>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm"
                   name="items[${itemCounter}][qty_diminta]"
                   id="qty-${itemCounter}"
                   min="1" value="1"
                   onchange="calculateSubtotal(${itemCounter})" required>
            <small class="text-muted" id="max-qty-${itemCounter}"></small>
        </td>
        <td class="text-end">
            <strong id="subtotal-${itemCounter}">Rp 0</strong>
            <input type="hidden" id="subtotal-val-${itemCounter}" value="0">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${itemCounter})">
                <i class="ri-delete-bin-line"></i>
            </button>
        </td>
    `;

    tbody.appendChild(row);
    initializeSelect2(itemCounter);
}

// ─────────────────────────────────────────────────────────
// INIT SELECT2 PRODUK
// ─────────────────────────────────────────────────────────
function initializeSelect2(itemId) {
    const selectEl = $(`#produk-${itemId}`);

    filteredProduk.forEach(p => {
        const text = isPenjualan
            ? `${p.nama} - Batch: ${p.no_batch} (Stock: ${p.stock_gudang})`
            : `${p.nama} - ${p.merk || ''} (${p.satuan})`;

        const opt = new Option(text, p.id, false, false);
        $(opt).data('produk', p);
        selectEl.append(opt);
    });

    selectEl.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Pilih Produk --',
        allowClear: true,
        templateResult:    formatProdukOption,
        templateSelection: formatProdukSelected
    });

    // Select2 change event
    selectEl.on('select2:select select2:unselect', function () {
        onProdukChange(itemId);
    });
}

// ─────────────────────────────────────────────────────────
// FORMAT OPTION SELECT2
// ─────────────────────────────────────────────────────────
function formatProdukOption(produk) {
    if (!produk.id) return produk.text;
    const d = $(produk.element).data('produk');
    if (!d) return produk.text;

    if (isPenjualan) {
        return $(`<div>
            <strong>${d.nama}</strong> ${d.merk ? '- ' + d.merk : ''}
            <br>
            <small>
                <span class="badge bg-info stock-badge">Batch: ${d.no_batch}</span>
                <span class="badge bg-success stock-badge">Stock: ${d.stock_gudang}</span>
                ${d.tanggal_kadaluarsa !== '-'
                    ? `<span class="badge bg-warning stock-badge text-dark">Exp: ${d.tanggal_kadaluarsa}</span>`
                    : ''}
            </small>
        </div>`);
    } else {
        return $(`<div>
            <strong>${d.nama}</strong> - ${d.merk || ''} (${d.satuan})
            <br>
            <small>
                <span class="badge bg-primary stock-badge">Jenis: ${d.jenis}</span>
                <span class="badge bg-success stock-badge">Harga: Rp ${fmt(d.harga_beli)}</span>
            </small>
        </div>`);
    }
}

function formatProdukSelected(produk) {
    if (!produk.id) return produk.text;
    const d = $(produk.element).data('produk');
    if (!d) return produk.text;
    return isPenjualan
        ? `${d.nama} - Batch: ${d.no_batch}`
        : `${d.nama} - ${d.jenis}`;
}

// ─────────────────────────────────────────────────────────
// EVENT: PRODUK DIPILIH
// ─────────────────────────────────────────────────────────
function onProdukChange(itemId) {
    const selectEl = $(`#produk-${itemId}`);
    const data     = selectEl.find(':selected').data('produk');

    // Reset harga & subtotal
    setHarga(itemId, 0);
    calculateSubtotal(itemId);

    if (!data) {
        if (isPenjualan) {
            const satuanSel = document.getElementById(`satuan-${itemId}`);
            satuanSel.innerHTML = '<option value="">-- Pilih Satuan --</option>';
            satuanSel.disabled = true;
            document.getElementById(`satuan-info-${itemId}`).textContent = '';
        }
        document.getElementById(`batch-info-${itemId}`).innerHTML = '';
        return;
    }

    if (isPenjualan) {
        // ── Isi dropdown satuan dari data produk ──
        populateSatuan(itemId, data);

        // Tampilkan info batch & stok
        document.getElementById(`batch-info-${itemId}`).innerHTML = `
            <span class="badge bg-info stock-badge">Batch: ${data.no_batch}</span>
            <span class="badge bg-success stock-badge">Stock: ${data.stock_gudang}</span>
            ${data.tanggal_kadaluarsa !== '-'
                ? `<span class="badge bg-warning stock-badge text-dark">Exp: ${data.tanggal_kadaluarsa}</span>`
                : ''}
        `;

        // Set max qty dari stok
        const qtyInput = document.getElementById(`qty-${itemId}`);
        qtyInput.max   = data.stock_gudang;
        document.getElementById(`max-qty-${itemId}`).textContent = `Stok: ${data.stock_gudang}`;

    } else {
        // Pembelian: langsung set harga dari detail supplier
        const harga = parseFloat(data.harga_beli || 0);
        setHarga(itemId, harga);

        const jenisInput = document.getElementById(`jenis-${itemId}`);
        if (jenisInput) jenisInput.value = data.jenis || '';

        document.getElementById(`batch-info-${itemId}`).innerHTML = `
            <span class="badge bg-primary stock-badge">Jenis: ${data.jenis || '-'}</span>
            <span class="badge bg-success stock-badge">Harga: Rp ${fmt(harga)}</span>
            <span class="badge bg-info stock-badge">${data.satuan || 'pcs'}</span>
        `;
        calculateSubtotal(itemId);
    }
}

// ─────────────────────────────────────────────────────────
// POPULATE DROPDOWN SATUAN (penjualan)
// ─────────────────────────────────────────────────────────
function populateSatuan(itemId, produkData) {
    const satuanSel = document.getElementById(`satuan-${itemId}`);
    satuanSel.innerHTML = '<option value="">-- Pilih Satuan --</option>';

    const satuans = produkData.satuans || [];

    if (satuans.length === 0) {
        // Fallback: tidak ada satuan jual terdefinisi, pakai harga_jual langsung
        satuanSel.disabled = true;
        setHarga(itemId, parseFloat(produkData.harga_jual || 0));
        document.getElementById(`satuan-info-${itemId}`).textContent = 'Harga satuan default digunakan';
        calculateSubtotal(itemId);
        return;
    }

    satuans.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = `${s.label} — Rp ${fmt(s.harga_jual)}`;
        opt.dataset.harga = s.harga_jual;
        opt.dataset.isi   = s.isi;
        opt.dataset.label = s.label;
        satuanSel.appendChild(opt);
    });

    satuanSel.disabled = false;

    // Auto-pilih satuan default jika ada
    const defaultSatuan = satuans.find(s => s.is_default) || satuans[0];
    if (defaultSatuan) {
        satuanSel.value = defaultSatuan.id;
        updatePriceFromSatuan(itemId);
    }
}

// ─────────────────────────────────────────────────────────
// EVENT: SATUAN DIPILIH → update harga
// ─────────────────────────────────────────────────────────
function updatePriceFromSatuan(itemId) {
    const satuanSel = document.getElementById(`satuan-${itemId}`);
    const selected  = satuanSel.options[satuanSel.selectedIndex];

    if (!selected || !selected.value) {
        setHarga(itemId, 0);
        document.getElementById(`satuan-info-${itemId}`).textContent = '';
        calculateSubtotal(itemId);
        return;
    }

    const harga = parseFloat(selected.dataset.harga || 0);
    const isi   = parseFloat(selected.dataset.isi   || 1);
    const label = selected.dataset.label || '';

    setHarga(itemId, harga);

    document.getElementById(`satuan-info-${itemId}`).innerHTML =
        `<span class="badge bg-secondary stock-badge">1 ${label} = ${isi} satuan dasar</span>`;

    document.getElementById(`harga-info-${itemId}`).textContent =
        `Rp ${fmt(harga)} / ${label}`;

    calculateSubtotal(itemId);
}

// ─────────────────────────────────────────────────────────
// SET HARGA KE INPUT
// ─────────────────────────────────────────────────────────
function setHarga(itemId, harga) {
    document.getElementById(`harga-${itemId}`).value     = fmt(harga);
    document.getElementById(`harga-val-${itemId}`).value = harga;
}

// ─────────────────────────────────────────────────────────
// HITUNG SUBTOTAL PER BARIS
// ─────────────────────────────────────────────────────────
function calculateSubtotal(itemId) {
    const qtyInput = document.getElementById(`qty-${itemId}`);
    if (!qtyInput) return;

    let qty = parseInt(qtyInput.value || 0);

    // Validasi stok (penjualan)
    if (isPenjualan) {
        const selectEl = $(`#produk-${itemId}`);
        const d = selectEl.find(':selected').data('produk');
        if (d && qty > d.stock_gudang) {
            Swal.fire({
                icon: 'warning',
                title: 'Melebihi Stok',
                text: `Stok tersedia: ${d.stock_gudang}`
            });
            qty = d.stock_gudang;
            qtyInput.value = qty;
        }
    }

    const harga    = parseFloat(document.getElementById(`harga-val-${itemId}`).value || 0);
    const subtotal = harga * qty;

    document.getElementById(`subtotal-${itemId}`).textContent    = 'Rp ' + fmt(subtotal);
    document.getElementById(`subtotal-val-${itemId}`).value      = subtotal;

    calculateTotal();
}

// ─────────────────────────────────────────────────────────
// HAPUS BARIS
// ─────────────────────────────────────────────────────────
function removeItem(itemId) {
    const totalRows = document.querySelectorAll('#itemTableBody tr').length;

    if (totalRows <= 1) {
        Swal.fire({ icon: 'warning', title: 'Tidak Dapat Menghapus', text: 'Minimal harus ada 1 item' });
        return;
    }

    Swal.fire({
        title: 'Hapus Item?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById(`item-${itemId}`).remove();
            calculateTotal();
            renumberRows();
        }
    });
}

// ─────────────────────────────────────────────────────────
// RENUMBER SETELAH HAPUS
// ─────────────────────────────────────────────────────────
function renumberRows() {
    document.querySelectorAll('#itemTableBody tr').forEach((row, i) => {
        const newIdx = i + 1;
        const oldId  = row.id.split('-')[1];
        row.id = `item-${newIdx}`;
        row.querySelector('td:first-child').textContent = newIdx;

        // Update semua id & name
        row.querySelectorAll('[id]').forEach(el => {
            el.id = el.id.replace(new RegExp(`-${oldId}$`), `-${newIdx}`);
        });
        row.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${newIdx}]`);
        });
        row.querySelectorAll('[onchange]').forEach(el => {
            el.setAttribute('onchange', el.getAttribute('onchange').replace(/\(\d+\)/, `(${newIdx})`));
        });
        row.querySelectorAll('[onclick]').forEach(el => {
            el.setAttribute('onclick', el.getAttribute('onclick').replace(/\(\d+\)/, `(${newIdx})`));
        });
    });

    itemCounter = document.querySelectorAll('#itemTableBody tr').length;
}

// ─────────────────────────────────────────────────────────
// HITUNG GRAND TOTAL
// ─────────────────────────────────────────────────────────
function calculateTotal() {
    let itemCount = 0, totalQty = 0, total = 0;

    document.querySelectorAll('#itemTableBody tr').forEach(row => {
        const qtyEl = row.querySelector('input[id^="qty-"]');
        if (!qtyEl) return;

        const qty = parseInt(qtyEl.value || 0);
        const id  = qtyEl.id.split('-')[1];

        if (qty > 0) { itemCount++; totalQty += qty; }

        const subEl = document.getElementById(`subtotal-val-${id}`);
        if (subEl) total += parseFloat(subEl.value || 0);
    });

    const pajakPersen = parseFloat(document.getElementById('pajak_persen')?.value || 0);
    const pajak       = (total * pajakPersen) / 100;
    const grand       = total + pajak;

    const pajakValEl = document.getElementById('pajak_value');
    if (pajakValEl) pajakValEl.value = pajak;

    document.getElementById('summaryItemCount').textContent    = itemCount;
    document.getElementById('summaryTotalQty').textContent     = totalQty;
    document.getElementById('totalHarga').textContent          = 'Rp ' + fmt(total);
    document.getElementById('totalPajak').textContent          = 'Rp ' + fmt(pajak);
    document.getElementById('grandTotal').textContent          = 'Rp ' + fmt(grand);
    document.getElementById('summarySubtotal').textContent     = 'Rp ' + fmt(total);
    document.getElementById('summaryPajak').textContent        = 'Rp ' + fmt(pajak);
    document.getElementById('summaryGrandTotal').textContent   = 'Rp ' + fmt(grand);
    document.getElementById('nilai_pajak_display').textContent = 'Rp ' + fmt(pajak);
}

// ─────────────────────────────────────────────────────────
// FORMAT ANGKA RUPIAH
// ─────────────────────────────────────────────────────────
function fmt(n) {
    return new Intl.NumberFormat('id-ID').format(Math.round(n));
}

// ─────────────────────────────────────────────────────────
// EVENT LISTENERS
// ─────────────────────────────────────────────────────────
document.getElementById('pajak_persen')?.addEventListener('input', calculateTotal);

document.getElementById('formPO').addEventListener('submit', function (e) {
    const itemCount = document.querySelectorAll('#itemTableBody tr').length;

    if (itemCount === 0) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Tambahkan minimal 1 item' });
        return false;
    }

    // Validasi satuan untuk penjualan
    @if($type === 'penjualan')
        let missingSatuan = false;

        document.querySelectorAll('select[id^="satuan-"]').forEach(sel => {
            if (!/^satuan-\d+$/.test(sel.id)) return;
            if (!sel.disabled && (!sel.value || sel.value === '')) {
                missingSatuan = true;
            }
        });

        if (missingSatuan) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Satuan Belum Dipilih',
                text: 'Pilih satuan untuk semua item terlebih dahulu'
            });
            return false;
        }
        @endif

    Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
});
</script>
@endpush