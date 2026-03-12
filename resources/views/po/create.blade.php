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
    .price-info {
        font-size: 0.75rem;
        color: #28a745;
        font-weight: 500;
    }
    .conversion-info {
        font-size: 0.7rem;
        color: #6c757d;
        font-style: italic;
    }
    .discount-info {
        font-size: 0.7rem;
        color: #dc3545;
    }

    /* FREE Toggle Switch */
    .free-toggle-wrapper {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
    }
    .free-toggle {
        position: relative;
        display: inline-block;
        width: 42px;
        height: 22px;
        flex-shrink: 0;
    }
    .free-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .free-toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #ccc;
        border-radius: 22px;
        transition: .3s;
    }
    .free-toggle-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        border-radius: 50%;
        transition: .3s;
    }
    .free-toggle input:checked + .free-toggle-slider {
        background-color: #0d6efd;
    }
    .free-toggle input:checked + .free-toggle-slider:before {
        transform: translateX(20px);
    }
    .free-toggle-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6c757d;
        user-select: none;
        cursor: pointer;
    }
    .free-toggle-label.is-free {
        color: #0d6efd;
    }
    .free-badge {
        display: none;
        font-size: 0.65rem;
        background: #0d6efd;
        color: white;
        padding: 1px 6px;
        border-radius: 10px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .free-badge.visible {
        display: inline-block;
    }

    /* Diskon input */
    .diskon-input-group {
        display: flex;
        align-items: center;
        gap: 3px;
    }
    .diskon-input-group input {
        width: 60px;
        text-align: right;
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
                                    <i class="ri-checkbox-circle-line me-1"></i> Stok akan dikurangi dari Gudang setelah pengiriman<br>
                                    <i class="ri-checkbox-circle-line me-1"></i> Harga diambil dari <strong>Detail Customer</strong> per produk & satuan
                                </small>
                            @else
                                <strong>PO Pembelian:</strong> Pembelian barang dari <strong>Supplier</strong> ke <strong>Gudang</strong><br>
                                <small class="text-muted">
                                    <i class="ri-checkbox-circle-line me-1"></i> Stok akan ditambah ke Gudang setelah diterima<br>
                                    <i class="ri-checkbox-circle-line me-1"></i> Harga diambil dari <strong>Detail Supplier</strong> per produk & satuan
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
                                        <th width="35">No</th>
                                        <th>Produk <span class="text-danger">*</span></th>
                                        <th width="150">Satuan <span class="text-danger">*</span></th>
                                        <th width="120">Harga</th>
                                        <th width="130">Diskon</th>
                                        <th width="85">Qty <span class="text-danger">*</span></th>
                                        <th width="120" class="text-end">Subtotal</th>
                                        <th width="55" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemTableBody">
                                    {{-- Rows ditambah via JS --}}
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="6" class="text-end">Total:</th>
                                        <th class="text-end" id="totalHarga">Rp 0</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="6" class="text-end">Pajak:</th>
                                        <th class="text-end" id="totalPajak">Rp 0</th>
                                        <th></th>
                                    </tr>
                                    <tr class="table-primary">
                                        <th colspan="6" class="text-end">Grand Total:</th>
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
                            <span class="text-muted">Diskon:</span>
                            <strong class="text-danger" id="summaryDiskon">- Rp 0</strong>
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
                                <li class="mb-2">Pilih <strong>Satuan</strong> setelah memilih produk</li>
                                <li class="mb-2 text-info"><strong>Info:</strong> Stok dalam PCS, harga per satuan yang dipilih</li>
                                <li class="mb-2">Aktifkan toggle <strong>FREE</strong> untuk item gratis</li>
                                <li class="mb-2">Stok akan dikurangi setelah pengiriman disetujui</li>
                            @else
                                <li class="mb-2"><strong class="text-primary">Harga otomatis dari Detail Supplier</strong></li>
                                <li class="mb-2">Satuan menentukan konversi ke PCS</li>
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
// ─────────────────────────────────────────────────────────
// DATA GLOBAL
// ─────────────────────────────────────────────────────────
let itemCounter = 0;

const produkData = JSON.parse('{!! addslashes(json_encode($produkList)) !!}');
const isPenjualan = {{ $type === 'penjualan' ? 'true' : 'false' }};
const urlGetHargaCustomer = "{{ route('po.get-harga-customer') }}";

let filteredProduk = [];

// ─────────────────────────────────────────────────────────
// RELASI CHANGE (Supplier / Customer)
// ─────────────────────────────────────────────────────────
const relasiSelect = document.getElementById('relasi');

relasiSelect.addEventListener('change', function () {
    const relasiId = this.value;

    if (isPenjualan) {
        if (relasiId) {
            filteredProduk = [...produkData].sort((a, b) => {
                const parseDate = (str) => {
                    if (!str || str === '-') return null;
                    const [d, m, y] = str.split('/');
                    return new Date(`${y}-${m}-${d}`);
                };
                const dateA = parseDate(a.tanggal_kadaluarsa);
                const dateB = parseDate(b.tanggal_kadaluarsa);
                if (!dateA && !dateB) return 0;
                if (!dateA) return 1;
                if (!dateB) return -1;
                return dateA - dateB;
            });
        } else {
            filteredProduk = [];
        }
    } else {
        filteredProduk = relasiId
            ? produkData.filter(p => String(p.supplier_id) === String(relasiId))
            : [];
    }

    document.getElementById('itemTableBody').innerHTML = '';
    itemCounter = 0;
    calculateTotal();
});

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
            text: isPenjualan
                ? 'Tidak ada stok produk di gudang'
                : 'Supplier ini belum memiliki produk atau harga di Detail Supplier'
        });
        return;
    }

    itemCounter++;
    const tbody = document.getElementById('itemTableBody');
    const row   = document.createElement('tr');
    row.className = 'item-row';
    row.id = `item-${itemCounter}`;

    row.innerHTML = `
        <td class="text-center">${itemCounter}</td>
        <td>
            <input type="hidden" name="items[${itemCounter}][detail_gudang_id]" id="detail-gudang-id-${itemCounter}" value="">
            <input type="hidden" name="items[${itemCounter}][no_batch]" id="no-batch-${itemCounter}" value="">
            <input type="hidden" name="items[${itemCounter}][tanggal_kadaluarsa]" id="tanggal-kadaluarsa-${itemCounter}" value="">
            <select class="form-select form-select-sm select2-produk" id="produk-${itemCounter}" required>
                <option value="">-- Pilih Produk --</option>
            </select>
            <input type="hidden" name="items[${itemCounter}][id_produk]" id="produk-id-real-${itemCounter}" value="">
            <div class="batch-info" id="batch-info-${itemCounter}"></div>
        </td>
        <td>
            <select class="form-select form-select-sm"
                    id="satuan-${itemCounter}"
                    name="items[${itemCounter}][produk_satuan_id]"
                    onchange="onSatuanChange(${itemCounter})"
                    disabled required>
                <option value="">-- Pilih Satuan --</option>
            </select>
            <div class="conversion-info" id="conversion-info-${itemCounter}"></div>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm text-end bg-light"
                   id="harga-${itemCounter}" readonly value="0">
            <input type="hidden" name="items[${itemCounter}][harga_asli]" id="harga-asli-val-${itemCounter}" value="0">
            <input type="hidden" name="items[${itemCounter}][harga]" id="harga-val-${itemCounter}" value="0">
            <div class="price-info" id="price-info-${itemCounter}"></div>
        </td>
        <td>
            <!-- Diskon % input -->
            <div class="diskon-input-group mb-1">
                <input type="number" class="form-control form-control-sm"
                       id="diskon-persen-${itemCounter}"
                       name="items[${itemCounter}][diskon_persen]"
                       min="0" max="100" step="0.01" value="0"
                       placeholder="0"
                       oninput="onDiskonChange(${itemCounter})"
                       title="Diskon (%)">
                <span class="text-muted small">%</span>
            </div>
            <!-- FREE Toggle -->
            <div class="free-toggle-wrapper">
                <label class="free-toggle" title="Aktifkan untuk FREE / Gratis">
                    <input type="checkbox" id="free-toggle-${itemCounter}"
                           onchange="onFreeToggle(${itemCounter})">
                    <span class="free-toggle-slider"></span>
                </label>
                <span class="free-toggle-label" id="free-toggle-label-${itemCounter}">FREE</span>
                <span class="free-badge" id="free-badge-${itemCounter}">GRATIS</span>
            </div>
            <input type="hidden" name="items[${itemCounter}][is_free]" id="is-free-${itemCounter}" value="0">
            <input type="hidden" name="items[${itemCounter}][diskon_nominal]" id="diskon-nominal-${itemCounter}" value="0">
            <div class="discount-info" id="discount-info-${itemCounter}"></div>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm"
                   name="items[${itemCounter}][qty_diminta]"
                   id="qty-${itemCounter}"
                   min="1" value="1"
                   onchange="calculateSubtotal(${itemCounter})"
                   oninput="calculateSubtotal(${itemCounter})" required>
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
// DISKON CHANGE
// ─────────────────────────────────────────────────────────
function onDiskonChange(itemId) {
    const freeToggle = document.getElementById(`free-toggle-${itemId}`);
    if (freeToggle && freeToggle.checked) {
        // Jika FREE aktif, abaikan input diskon manual
        return;
    }

    let persen = parseFloat(document.getElementById(`diskon-persen-${itemId}`).value || 0);
    if (persen < 0) persen = 0;
    if (persen > 100) {
        persen = 100;
        document.getElementById(`diskon-persen-${itemId}`).value = 100;
    }

    const hargaAsli = parseFloat(document.getElementById(`harga-asli-val-${itemId}`).value || 0);
    const qty       = parseInt(document.getElementById(`qty-${itemId}`)?.value || 1);
    const nominal   = (hargaAsli * persen) / 100;
    const hargaAfterDiskon = hargaAsli - nominal;

    document.getElementById(`harga-val-${itemId}`).value = hargaAfterDiskon;
    document.getElementById(`diskon-nominal-${itemId}`).value = nominal;

    const discountInfoEl = document.getElementById(`discount-info-${itemId}`);
    if (persen > 0) {
        discountInfoEl.textContent = `Hemat Rp ${fmt(nominal)} / item`;
    } else {
        discountInfoEl.textContent = '';
    }

    calculateSubtotal(itemId);
}

// ─────────────────────────────────────────────────────────
// FREE TOGGLE
// ─────────────────────────────────────────────────────────
function onFreeToggle(itemId) {
    const freeToggle = document.getElementById(`free-toggle-${itemId}`);
    const isFree     = freeToggle.checked;
    const labelEl    = document.getElementById(`free-toggle-label-${itemId}`);
    const badgeEl    = document.getElementById(`free-badge-${itemId}`);
    const isFreeHidden = document.getElementById(`is-free-${itemId}`);
    const diskonPersenInput = document.getElementById(`diskon-persen-${itemId}`);
    const discountInfoEl    = document.getElementById(`discount-info-${itemId}`);

    if (isFree) {
        // Set FREE: diskon = 100%, harga jadi 0
        const hargaAsli = parseFloat(document.getElementById(`harga-asli-val-${itemId}`).value || 0);

        diskonPersenInput.value  = 100;
        diskonPersenInput.disabled = true;

        document.getElementById(`harga-val-${itemId}`).value     = 0;
        document.getElementById(`diskon-nominal-${itemId}`).value = hargaAsli;

        isFreeHidden.value = 1;
        labelEl.classList.add('is-free');
        badgeEl.classList.add('visible');
        discountInfoEl.innerHTML = '<span class="text-primary fw-bold">✓ Item ini GRATIS</span>';
    } else {
        // Unset FREE: kembalikan ke diskon sebelumnya
        diskonPersenInput.disabled = false;
        isFreeHidden.value = 0;
        labelEl.classList.remove('is-free');
        badgeEl.classList.remove('visible');

        // Reset diskon ke 0
        diskonPersenInput.value = 0;
        const hargaAsli = parseFloat(document.getElementById(`harga-asli-val-${itemId}`).value || 0);
        document.getElementById(`harga-val-${itemId}`).value     = hargaAsli;
        document.getElementById(`diskon-nominal-${itemId}`).value = 0;
        discountInfoEl.textContent = '';
    }

    calculateSubtotal(itemId);
}

// ─────────────────────────────────────────────────────────
// INIT SELECT2 PRODUK
// ─────────────────────────────────────────────────────────
function initializeSelect2(itemId) {
    const selectEl = $(`#produk-${itemId}`);

    selectEl.select2({
        theme:       'bootstrap-5',
        width:       '100%',
        placeholder: '-- Ketik nama produk untuk mencari... --',
        allowClear:  true,
        minimumInputLength: 1,
        language: {
            inputTooShort: () => 'Ketik minimal 1 huruf untuk mencari produk...',
            noResults:     () => 'Produk tidak ditemukan',
            searching:     () => 'Mencari...',
        },
        templateResult:    formatProdukOption,
        templateSelection: formatProdukSelected,
        data: [],
        ajax: {
            transport: function (params, success) {
                const term  = (params.data.term || '').toLowerCase().trim();
                const results = filteredProduk
                    .filter(p => {
                        const haystack = [p.nama, p.merk, p.no_batch, p.supplier_name].join(' ').toLowerCase();
                        return haystack.includes(term);
                    })
                    .map(p => {
                        let text = '';
                        if (isPenjualan) {
                            text = `${p.nama}${p.merk ? ' - ' + p.merk : ''}`;
                            text += ` | ${p.supplier_name} | Batch: ${p.no_batch}`;
                            text += ` (Stok: ${p.stock_gudang} PCS)`;
                        } else {
                            text = `${p.nama}${p.merk ? ' - ' + p.merk : ''}`;
                            text += ` (${p.satuan} — 1:${p.konversi} PCS)`;
                        }
                        return { id: p.id, text, _data: p };
                    });
                success({ results });
            },
            delay: 150,
        },
    });

    selectEl.on('select2:select', function (e) {
        const chosen = e.params.data;
        $(this).find(`option[value="${chosen.id}"]`).data('produk', chosen._data);
        onProdukChange(itemId);
    });

    selectEl.on('select2:unselect', function () {
        onProdukChange(itemId);
    });
}

// ─────────────────────────────────────────────────────────
// FORMAT OPTION SELECT2
// ─────────────────────────────────────────────────────────
function formatProdukOption(item) {
    if (!item.id) return item.text;
    const d = item._data || $(item.element).data('produk');
    if (!d) return item.text;

    if (isPenjualan) {
        return $(`<div>
            <strong>${d.nama}</strong>${d.merk ? ' <span style="color:#6c757d">- ' + d.merk + '</span>' : ''}
            <br>
            <small>
                <span class="badge bg-secondary stock-badge">Supplier: ${d.supplier_name || '-'}</span>
                <span class="badge bg-info stock-badge">Batch: ${d.no_batch || '-'}</span>
                <span class="badge bg-success stock-badge">Stok: ${d.stock_gudang} PCS</span>
                ${d.tanggal_kadaluarsa && d.tanggal_kadaluarsa !== '-'
                    ? `<span class="badge bg-warning stock-badge text-dark">Exp: ${d.tanggal_kadaluarsa}</span>`
                    : '<span class="badge bg-secondary stock-badge">Exp: -</span>'}
            </small>
        </div>`);
    } else {
        return $(`<div>
            <strong>${d.nama}</strong>${d.merk ? ' <span style="color:#6c757d">- ' + d.merk + '</span>' : ''}
            <br>
            <small>
                <span class="badge bg-primary stock-badge">Harga: Rp ${fmt(d.harga_beli)}</span>
                <span class="badge bg-info stock-badge">Satuan: ${d.satuan}</span>
                <span class="badge bg-dark stock-badge">1 ${d.satuan} = ${d.konversi} PCS</span>
            </small>
        </div>`);
    }
}

function formatProdukSelected(item) {
    if (!item.id) return item.text;
    const d = item._data || $(item.element).data('produk');
    if (!d) return item.text;
    if (isPenjualan) {
        return `${d.nama} — ${d.supplier_name}${d.no_batch && d.no_batch !== '-' ? ' | Batch: ' + d.no_batch : ''}`;
    } else {
        return `${d.nama} — ${d.satuan} (1:${d.konversi} PCS)`;
    }
}

// ─────────────────────────────────────────────────────────
// EVENT: PRODUK DIPILIH
// ─────────────────────────────────────────────────────────
function onProdukChange(itemId) {
    const satuanSel   = document.getElementById(`satuan-${itemId}`);
    const select2Data = $(`#produk-${itemId}`).select2('data');
    const data = (select2Data && select2Data[0])
        ? (select2Data[0]._data || $(`#produk-${itemId}`).find(':selected').data('produk'))
        : null;

    // Reset semua field
    document.getElementById(`harga-${itemId}`).value                 = '0';
    document.getElementById(`harga-val-${itemId}`).value             = '0';
    document.getElementById(`harga-asli-val-${itemId}`).value        = '0';
    document.getElementById(`price-info-${itemId}`).textContent      = '';
    document.getElementById(`conversion-info-${itemId}`).textContent = '';
    document.getElementById(`batch-info-${itemId}`).innerHTML        = '';
    document.getElementById(`max-qty-${itemId}`).textContent         = '';
    // Reset diskon
    document.getElementById(`diskon-persen-${itemId}`).value         = '0';
    document.getElementById(`diskon-persen-${itemId}`).disabled      = false;
    document.getElementById(`diskon-nominal-${itemId}`).value        = '0';
    document.getElementById(`discount-info-${itemId}`).textContent   = '';
    document.getElementById(`free-toggle-${itemId}`).checked         = false;
    document.getElementById(`is-free-${itemId}`).value               = '0';
    document.getElementById(`free-toggle-label-${itemId}`).classList.remove('is-free');
    document.getElementById(`free-badge-${itemId}`).classList.remove('visible');

    satuanSel.innerHTML = '<option value="">-- Pilih Satuan --</option>';
    satuanSel.disabled  = true;
    document.getElementById(`produk-id-real-${itemId}`).value = '';

    if (!data) { calculateSubtotal(itemId); return; }

    document.getElementById(`produk-id-real-${itemId}`).value =
        isPenjualan ? (data.produk_id || '') : (data.id || '');

    if (isPenjualan) {
        document.getElementById(`detail-gudang-id-${itemId}`).value = data.detail_gudang_id || '';
        document.getElementById(`no-batch-${itemId}`).value =
            (data.no_batch && data.no_batch !== '-') ? data.no_batch : '';

        const rawExp = data.tanggal_kadaluarsa;
        let expForServer = '';
        if (rawExp && rawExp !== '-') {
            const parts = rawExp.split('/');
            if (parts.length === 3) expForServer = `${parts[2]}-${parts[1]}-${parts[0]}`;
        }
        document.getElementById(`tanggal-kadaluarsa-${itemId}`).value = expForServer;
        document.getElementById(`batch-info-${itemId}`).innerHTML = `
            <span class="badge bg-secondary stock-badge">Supplier: ${data.supplier_name || '-'}</span>
            <span class="badge bg-info stock-badge">Batch: ${data.no_batch || '-'}</span>
            <span class="badge bg-success stock-badge">Stok: ${data.stock_gudang} PCS</span>
            ${data.tanggal_kadaluarsa !== '-'
                ? `<span class="badge bg-warning stock-badge text-dark">Exp: ${data.tanggal_kadaluarsa}</span>`
                : ''}
        `;

        const qtyInput = document.getElementById(`qty-${itemId}`);
        qtyInput.value = 1;
        document.getElementById(`max-qty-${itemId}`).textContent = `Stok: ${data.stock_gudang} PCS — pilih satuan`;

        if (data.satuans && data.satuans.length > 0) {
            data.satuans.forEach(s => {
                const option            = document.createElement('option');
                option.value            = s.id;
                option.textContent      = `${s.label} (1 ${s.label} = ${s.konversi} PCS)`;
                option.dataset.konversi = s.konversi;
                option.dataset.label    = s.label;
                satuanSel.appendChild(option);
            });
            satuanSel.disabled = false;
            document.getElementById(`price-info-${itemId}`).textContent = 'Pilih satuan untuk melihat harga';
        } else {
            document.getElementById(`batch-info-${itemId}`).insertAdjacentHTML('beforeend',
                '<span class="badge bg-danger stock-badge">⚠ Satuan belum diset</span>');
        }
    } else {
        const harga    = parseFloat(data.harga_beli || 0);
        const konversi = parseInt(data.konversi || 1);
        const satuan   = data.satuan || 'PCS';

        document.getElementById(`batch-info-${itemId}`).innerHTML = `
            <span class="badge bg-primary stock-badge">Rp ${fmt(harga)} / ${satuan}</span>
            <span class="badge bg-dark stock-badge">1 ${satuan} = ${konversi} PCS</span>
        `;
        document.getElementById(`harga-${itemId}`).value          = fmt(harga);
        document.getElementById(`harga-val-${itemId}`).value      = harga;
        document.getElementById(`harga-asli-val-${itemId}`).value = harga;
        document.getElementById(`price-info-${itemId}`).textContent = `Rp ${fmt(harga)} / ${satuan}`;
        document.getElementById(`conversion-info-${itemId}`).textContent =
            `1 ${satuan} = ${konversi} PCS — qty 1 = ${konversi} PCS ke gudang`;

        const option            = document.createElement('option');
        option.value            = data.produk_satuan_id || '';
        option.textContent      = `${satuan} (1 ${satuan} = ${konversi} PCS)`;
        option.dataset.konversi = konversi;
        option.dataset.label    = satuan;
        option.dataset.harga    = harga;
        satuanSel.appendChild(option);
        satuanSel.disabled = false;
        satuanSel.value    = data.produk_satuan_id || '';
    }

    calculateSubtotal(itemId);
}

// ─────────────────────────────────────────────────────────
// EVENT: SATUAN DIPILIH
// ─────────────────────────────────────────────────────────
async function onSatuanChange(itemId) {
    const satuanSel   = document.getElementById(`satuan-${itemId}`);
    const selectedOpt = satuanSel.options[satuanSel.selectedIndex];

    const select2Data = $(`#produk-${itemId}`).select2('data');
    const produkEl    = (select2Data && select2Data[0])
        ? (select2Data[0]._data || $(`#produk-${itemId}`).find(':selected').data('produk'))
        : null;

    // Reset harga & diskon
    document.getElementById(`harga-${itemId}`).value              = '0';
    document.getElementById(`harga-val-${itemId}`).value          = '0';
    document.getElementById(`harga-asli-val-${itemId}`).value     = '0';
    document.getElementById(`price-info-${itemId}`).textContent   = '';
    document.getElementById(`conversion-info-${itemId}`).textContent = '';
    document.getElementById(`diskon-persen-${itemId}`).value      = '0';
    document.getElementById(`diskon-persen-${itemId}`).disabled   = false;
    document.getElementById(`diskon-nominal-${itemId}`).value     = '0';
    document.getElementById(`discount-info-${itemId}`).textContent = '';
    document.getElementById(`free-toggle-${itemId}`).checked      = false;
    document.getElementById(`is-free-${itemId}`).value            = '0';
    document.getElementById(`free-toggle-label-${itemId}`).classList.remove('is-free');
    document.getElementById(`free-badge-${itemId}`).classList.remove('visible');

    if (!selectedOpt || !selectedOpt.value || !produkEl) {
        calculateSubtotal(itemId); return;
    }

    const konversi = parseInt(selectedOpt.dataset.konversi || 1);
    const label    = selectedOpt.dataset.label || 'PCS';
    document.getElementById(`conversion-info-${itemId}`).textContent = `1 ${label} = ${konversi} PCS`;

    if (isPenjualan) {
        const stockPcs = parseInt(produkEl.stock_gudang || 0);
        const maxQty   = Math.floor(stockPcs / konversi);
        const qtyInput = document.getElementById(`qty-${itemId}`);

        qtyInput.max   = maxQty;
        qtyInput.value = Math.min(parseInt(qtyInput.value || 1), Math.max(maxQty, 0));

        const maxQtyEl = document.getElementById(`max-qty-${itemId}`);
        if (maxQty > 0) {
            maxQtyEl.textContent = `Maks: ${maxQty} ${label} (${stockPcs} PCS tersedia)`;
            maxQtyEl.className   = 'text-muted';
        } else {
            maxQtyEl.innerHTML = `<span class="text-danger fw-bold">⚠ Stok tidak cukup! (${stockPcs} PCS &lt; ${konversi} PCS/unit)</span>`;
        }

        if (maxQty === 0) {
            qtyInput.value = 0;
            document.getElementById(`harga-${itemId}`).value     = '0';
            document.getElementById(`harga-val-${itemId}`).value = '0';
            document.getElementById(`harga-asli-val-${itemId}`).value = '0';
            document.getElementById(`price-info-${itemId}`).innerHTML =
                `<span class="text-danger">⚠ Stok tidak mencukupi untuk satuan ini</span>`;

            Swal.fire({
                icon: 'warning', title: 'Stok Tidak Cukup',
                text: `Stok tersedia ${stockPcs} PCS, tidak cukup untuk 1 ${label} (butuh ${konversi} PCS per unit).`,
                confirmButtonText: 'OK'
            });
            calculateSubtotal(itemId); return;
        }

        // Fetch harga dari Detail Customer
        const customerId     = document.getElementById('relasi').value;
        const produkId       = document.getElementById(`produk-id-real-${itemId}`).value;
        const produkSatuanId = selectedOpt.value;

        if (!customerId || !produkId) { calculateSubtotal(itemId); return; }

        document.getElementById(`price-info-${itemId}`).textContent = 'Mengambil harga...';
        document.getElementById(`harga-${itemId}`).value = '...';

        try {
            const params = new URLSearchParams({ customer_id: customerId, produk_id: produkId, produk_satuan_id: produkSatuanId });
            const res  = await fetch(`${urlGetHargaCustomer}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const json = await res.json();

            if (json.found && json.harga > 0) {
                document.getElementById(`harga-${itemId}`).value          = fmt(json.harga);
                document.getElementById(`harga-val-${itemId}`).value      = json.harga;
                document.getElementById(`harga-asli-val-${itemId}`).value = json.harga;
                document.getElementById(`price-info-${itemId}`).textContent = `Rp ${fmt(json.harga)} / ${label}`;
            } else {
                document.getElementById(`harga-${itemId}`).value     = '0';
                document.getElementById(`harga-val-${itemId}`).value = '0';
                document.getElementById(`harga-asli-val-${itemId}`).value = '0';
                document.getElementById(`price-info-${itemId}`).innerHTML =
                    `<span class="text-danger">⚠ ${json.message}</span>`;
                Swal.fire({
                    icon: 'warning', title: 'Harga Tidak Ditemukan',
                    text: `Harga produk ini untuk customer yang dipilih belum diset di Detail Customer.`,
                    confirmButtonText: 'OK'
                });
            }
        } catch (err) {
            document.getElementById(`harga-${itemId}`).value     = '0';
            document.getElementById(`harga-val-${itemId}`).value = '0';
            document.getElementById(`harga-asli-val-${itemId}`).value = '0';
            document.getElementById(`price-info-${itemId}`).innerHTML =
                `<span class="text-danger">⚠ Gagal mengambil harga</span>`;
            console.error('Fetch harga error:', err);
        }
    } else {
        const harga = parseFloat(produkEl.harga_beli || 0);
        document.getElementById(`harga-${itemId}`).value          = fmt(harga);
        document.getElementById(`harga-val-${itemId}`).value      = harga;
        document.getElementById(`harga-asli-val-${itemId}`).value = harga;
        document.getElementById(`price-info-${itemId}`).textContent = `Rp ${fmt(harga)} / ${label}`;
    }

    calculateSubtotal(itemId);
}

// ─────────────────────────────────────────────────────────
// HITUNG SUBTOTAL
// ─────────────────────────────────────────────────────────
function calculateSubtotal(itemId) {
    const qtyInput = document.getElementById(`qty-${itemId}`);
    if (!qtyInput) return;

    let qty = parseInt(qtyInput.value || 0);
    if (qty < 0) { qty = 0; qtyInput.value = 0; }

    if (isPenjualan) {
        const select2D = $(`#produk-${itemId}`).select2('data');
        const data     = (select2D && select2D[0])
            ? (select2D[0]._data || $(`#produk-${itemId}`).find(':selected').data('produk'))
            : null;

        if (data) {
            const stockPcs    = parseInt(data.stock_gudang || 0);
            const satuanSel   = document.getElementById(`satuan-${itemId}`);
            const selectedOpt = satuanSel?.options[satuanSel?.selectedIndex];
            const konversi    = parseInt(selectedOpt?.dataset?.konversi || 1);
            const label       = selectedOpt?.dataset?.label || 'unit';
            const maxQty      = Math.floor(stockPcs / konversi);

            if (qty > maxQty) {
                qty = maxQty;
                qtyInput.value = qty;
                Swal.fire({
                    icon: 'warning', title: 'Melebihi Stok',
                    text: `Maks ${maxQty} ${label} (stok: ${stockPcs} PCS, butuh ${konversi} PCS per unit)`,
                    timer: 3000, showConfirmButton: false,
                });
            }
            if (qty < 1 && maxQty > 0) { qty = 1; qtyInput.value = 1; }
        }
    } else {
        if (qty < 1) { qty = 1; qtyInput.value = 1; }
    }

    // Gunakan harga setelah diskon
    const hargaAfterDiskon = parseFloat(document.getElementById(`harga-val-${itemId}`).value || 0);
    const subtotal         = hargaAfterDiskon * qty;

    document.getElementById(`subtotal-${itemId}`).textContent = 'Rp ' + fmt(subtotal);
    document.getElementById(`subtotal-val-${itemId}`).value   = subtotal;

    calculateTotal();
}

// ─────────────────────────────────────────────────────────
// HAPUS BARIS
// ─────────────────────────────────────────────────────────
function removeItem(itemId) {
    if (document.querySelectorAll('#itemTableBody tr').length <= 1) {
        Swal.fire({ icon: 'warning', title: 'Tidak Dapat Menghapus', text: 'Minimal harus ada 1 item' });
        return;
    }
    Swal.fire({
        title: 'Hapus Item?', icon: 'question', showCancelButton: true,
        confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById(`item-${itemId}`).remove();
            calculateTotal();
            renumberRows();
        }
    });
}

// ─────────────────────────────────────────────────────────
// RENUMBER ROWS
// ─────────────────────────────────────────────────────────
function renumberRows() {
    document.querySelectorAll('#itemTableBody tr').forEach((row, i) => {
        const newIdx = i + 1;
        const oldId  = row.id.split('-')[1];
        row.id = `item-${newIdx}`;
        row.querySelector('td:first-child').textContent = newIdx;
        row.querySelectorAll('[id]').forEach(el => {
            el.id = el.id.replace(new RegExp(`-${oldId}$`), `-${newIdx}`);
        });
        row.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${newIdx}]`);
        });
        ['onchange', 'onclick', 'oninput'].forEach(attr => {
            row.querySelectorAll(`[${attr}]`).forEach(el => {
                const v = el.getAttribute(attr);
                if (v) el.setAttribute(attr, v.replace(/\(\d+\)/, `(${newIdx})`));
            });
        });
    });
    itemCounter = document.querySelectorAll('#itemTableBody tr').length;
}

// ─────────────────────────────────────────────────────────
// GRAND TOTAL
// ─────────────────────────────────────────────────────────
function calculateTotal() {
    let itemCount = 0, totalQty = 0, totalBruto = 0, totalDiskon = 0;

    document.querySelectorAll('#itemTableBody tr').forEach(row => {
        const qtyInput = row.querySelector('input[id^="qty-"]');
        if (!qtyInput) return;

        const qty = parseInt(qtyInput.value || 0);
        const id  = qtyInput.id.split('-')[1];

        const hargaAsli   = parseFloat(document.getElementById(`harga-asli-val-${id}`)?.value || 0);
        const diskonNom   = parseFloat(document.getElementById(`diskon-nominal-${id}`)?.value || 0);
        const sub         = parseFloat(document.getElementById(`subtotal-val-${id}`)?.value || 0);

        if (qty > 0) { itemCount++; totalQty += qty; }

        totalBruto  += hargaAsli * qty;
        totalDiskon += diskonNom * qty;
    });

    const totalAfterDiskon = totalBruto - totalDiskon;
    const pajakPersen      = parseFloat(document.getElementById('pajak_persen')?.value || 0);
    const pajak            = (totalAfterDiskon * pajakPersen) / 100;
    const grandTotal       = totalAfterDiskon + pajak;

    document.getElementById('pajak_value').value              = pajak;
    document.getElementById('summaryItemCount').textContent   = itemCount;
    document.getElementById('summaryTotalQty').textContent    = totalQty;
    document.getElementById('totalHarga').textContent         = 'Rp ' + fmt(totalAfterDiskon);
    document.getElementById('totalPajak').textContent         = 'Rp ' + fmt(pajak);
    document.getElementById('grandTotal').textContent         = 'Rp ' + fmt(grandTotal);
    document.getElementById('summarySubtotal').textContent    = 'Rp ' + fmt(totalBruto);
    document.getElementById('summaryDiskon').textContent      = '- Rp ' + fmt(totalDiskon);
    document.getElementById('summaryPajak').textContent       = 'Rp ' + fmt(pajak);
    document.getElementById('summaryGrandTotal').textContent  = 'Rp ' + fmt(grandTotal);
    document.getElementById('nilai_pajak_display').textContent = 'Rp ' + fmt(pajak);
}

// ─────────────────────────────────────────────────────────
// FORMAT ANGKA
// ─────────────────────────────────────────────────────────
function fmt(n) {
    return new Intl.NumberFormat('id-ID').format(Math.round(n));
}

// ─────────────────────────────────────────────────────────
// EVENT LISTENERS
// ─────────────────────────────────────────────────────────
document.getElementById('pajak_persen')?.addEventListener('input', calculateTotal);

document.getElementById('formPO').addEventListener('submit', function (e) {
    if (document.querySelectorAll('#itemTableBody tr').length === 0) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Tambahkan minimal 1 item' });
        return false;
    }

    let missing    = false;
    let stockError = false;

    document.querySelectorAll('#itemTableBody tr').forEach(row => {
        const produkIdReal = row.querySelector('input[id^="produk-id-real-"]');
        const satuanSel    = row.querySelector('select[id^="satuan-"]');
        const qtyInput     = row.querySelector('input[id^="qty-"]');

        if (!produkIdReal?.value) missing = true;
        if (!satuanSel?.disabled && !satuanSel?.value) missing = true;

        if (isPenjualan && qtyInput && parseInt(qtyInput.value || 0) === 0) {
            stockError = true;
        }
    });

    if (missing) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Pastikan semua produk dan satuan sudah dipilih' });
        return false;
    }

    if (stockError) {
        e.preventDefault();
        Swal.fire({
            icon: 'error', title: 'Stok Tidak Cukup',
            text: 'Terdapat item dengan qty 0 karena stok tidak mencukupi untuk satuan yang dipilih. Hapus item tersebut atau ganti satuannya.'
        });
        return false;
    }

    Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
});
</script>
@endpush