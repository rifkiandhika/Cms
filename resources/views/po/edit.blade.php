@extends('layouts.app')

@section('title', 'Edit Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('po.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item"><a href="{{ route('po.show', $po->id_po) }}">Detail PO</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Edit PO</li>
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
    .price-info { font-size: 0.75rem; color: #28a745; font-weight: 500; }
    .conversion-info { font-size: 0.7rem; color: #6c757d; font-style: italic; }
    @keyframes fadeIn { from { opacity:0; transform: translateY(-10px); } to { opacity:1; transform: translateY(0); } }
</style>
@endpush

@section('content')
<div class="app-body">

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('po.update', $po->id_po) }}" method="POST" id="formPO">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- ── Kolom Kiri ── --}}
            <div class="col-xl-8">

                {{-- Informasi Umum --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="ri-information-line me-2"></i>Informasi Purchase Order</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nomor PO</label>
                                <input type="text" class="form-control" value="{{ $po->no_po }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Pemohon</label>
                                <input type="text" class="form-control" value="{{ $po->karyawanPemohon->nama_lengkap ?? auth()->user()->name }}" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tipe PO</label>
                                <input type="text" class="form-control" value="{{ ucfirst($po->tipe_po) }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    {{ $po->tipe_po === 'penjualan' ? 'Customer' : 'Supplier' }}
                                </label>
                                @if($po->tipe_po === 'penjualan')
                                    <input type="text" class="form-control"
                                           value="{{ $po->customer->nama_customer ?? '-' }}" disabled>
                                @else
                                    <input type="text" class="form-control"
                                           value="{{ $po->supplier->nama_supplier ?? '-' }}" disabled>
                                @endif
                            </div>
                        </div>

                        @if($po->no_gr)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">No. GR</label>
                            <input type="text" class="form-control" value="{{ $po->no_gr }}" disabled>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea class="form-control @error('catatan_pemohon') is-invalid @enderror"
                                      name="catatan_pemohon" rows="3">{{ old('catatan_pemohon', $po->catatan_pemohon) }}</textarea>
                            @error('catatan_pemohon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Item PO --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="ri-shopping-cart-line me-2"></i>Item Purchase Order</h5>
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
                                        <th width="180">Satuan <span class="text-danger">*</span></th>
                                        <th width="80" class="text-center">Konversi</th>
                                        <th width="130">Harga/Satuan</th>
                                        <th width="90">Qty <span class="text-danger">*</span></th>
                                        <th width="140" class="text-end">Subtotal</th>
                                        <th width="60" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemTableBody"></tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="7" class="text-end">Total:</th>
                                        <th class="text-end" id="totalHarga">Rp 0</th>
                                    </tr>
                                    @if($po->pajak > 0)
                                    <tr>
                                        <th colspan="7" class="text-end">Pajak:</th>
                                        <th class="text-end">Rp {{ number_format($po->pajak, 0, ',', '.') }}</th>
                                    </tr>
                                    @endif
                                    <tr class="table-primary">
                                        <th colspan="7" class="text-end">Grand Total:</th>
                                        <th class="text-end" id="grandTotal">Rp 0</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- PIN --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning py-3">
                        <h5 class="mb-0"><i class="ri-lock-line me-2"></i>Konfirmasi PIN</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            <i class="ri-shield-check-line me-2"></i>
                            Masukkan PIN 6 digit untuk mengonfirmasi perubahan PO ini.
                        </div>
                        <label class="form-label fw-semibold">PIN (6 digit) <span class="text-danger">*</span></label>
                        
                        {{-- OTP Style PIN Input --}}
                        <div class="d-flex justify-content-center gap-2 mb-2">
                            <input type="password" class="pin-input form-control text-center" maxlength="1" data-index="0" style="width:50px;height:60px;font-size:24px;">
                            <input type="password" class="pin-input form-control text-center" maxlength="1" data-index="1" style="width:50px;height:60px;font-size:24px;">
                            <input type="password" class="pin-input form-control text-center" maxlength="1" data-index="2" style="width:50px;height:60px;font-size:24px;">
                            <input type="password" class="pin-input form-control text-center" maxlength="1" data-index="3" style="width:50px;height:60px;font-size:24px;">
                            <input type="password" class="pin-input form-control text-center" maxlength="1" data-index="4" style="width:50px;height:60px;font-size:24px;">
                            <input type="password" class="pin-input form-control text-center" maxlength="1" data-index="5" style="width:50px;height:60px;font-size:24px;">
                        </div>
                        <input type="hidden" name="pin" id="pin">
                        @error('pin')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- ── Kolom Kanan ── --}}
            <div class="col-xl-4">
                <div class="card shadow-sm border-0 mb-4 position-sticky" style="top: 20px;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="ri-calculator-line me-2"></i>Ringkasan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Item:</span><strong id="summaryItemCount">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Quantity:</span><strong id="summaryTotalQty">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total PCS:</span><strong id="summaryTotalPcs">0</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span><strong id="summarySubtotal">Rp 0</strong>
                        </div>
                        @if($po->pajak > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Pajak:</span>
                            <strong>Rp {{ number_format($po->pajak, 0, ',', '.') }}</strong>
                        </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Grand Total:</span>
                            <h5 class="text-success mb-0" id="summaryGrandTotal">Rp 0</h5>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="ri-save-line me-1"></i> Update PO
                            </button>
                            <a href="{{ route('po.show', $po->id_po) }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Batal
                            </a>
                        </div>
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
const isPenjualan = {{ $po->tipe_po === 'penjualan' ? 'true' : 'false' }};
const poPajak = {{ $po->pajak }};
const filteredProduk = @json($produkList);

// ✅ Existing items dengan informasi satuan yang lebih lengkap
const existingItems = @json($po->items->map(fn($item) => [
    'id_produk'        => $item->id_produk,
    'produk_satuan_id' => $item->produk_satuan_id,
    'qty_diminta'      => $item->qty_diminta,
    'harga_satuan'     => $item->harga_satuan,
    'nama_produk'      => $item->nama_produk,
    'konversi'         => $item->konversi_snapshot,
    'qty_diminta_satuan_dasar' => $item->qty_diminta_satuan_dasar,
]));

// ──────────────────────────────────────────────────────────
// PIN INPUT HANDLER
// ──────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    const pinInputs = document.querySelectorAll('.pin-input');
    
    pinInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value;
            if (!/^\d$/.test(value)) {
                e.target.value = '';
                return;
            }
            this.classList.add('filled');
            if (value && index < pinInputs.length - 1) {
                pinInputs[index + 1].focus();
            }
            updatePinValue();
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace') {
                if (!e.target.value && index > 0) {
                    pinInputs[index - 1].focus();
                    pinInputs[index - 1].value = '';
                    pinInputs[index - 1].classList.remove('filled');
                } else {
                    e.target.value = '';
                    e.target.classList.remove('filled');
                }
                updatePinValue();
            } else if (e.key === 'ArrowLeft' && index > 0) {
                e.preventDefault();
                pinInputs[index - 1].focus();
            } else if (e.key === 'ArrowRight' && index < pinInputs.length - 1) {
                e.preventDefault();
                pinInputs[index + 1].focus();
            }
        });
        
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').trim();
            if (/^\d{6}$/.test(pastedData)) {
                pastedData.split('').forEach((char, i) => {
                    if (pinInputs[i]) {
                        pinInputs[i].value = char;
                        pinInputs[i].classList.add('filled');
                    }
                });
                pinInputs[5].focus();
                updatePinValue();
            }
        });
        
        input.addEventListener('focus', function() { this.select(); });
    });
    
    function updatePinValue() {
        const pin = Array.from(pinInputs).map(i => i.value).join('');
        document.getElementById('pin').value = pin;
    }
});

// ──────────────────────────────────────────────────────────
// TAMBAH BARIS
// ──────────────────────────────────────────────────────────
function createRow(index, produkId = null, produkSatuanId = null, qty = 1, hargaAwal = 0) {
    itemCounter++;
    const id = itemCounter;
    const tbody = document.getElementById('itemTableBody');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.id = `item-${id}`;

    row.innerHTML = `
        <td class="text-center">${index + 1}</td>
        <td>
            <select class="form-select form-select-sm select2-produk"
                    name="items[${id}][id_produk]"
                    id="produk-${id}" required>
                <option value="">-- Pilih Produk --</option>
            </select>
            <div class="batch-info" id="batch-info-${id}"></div>
        </td>
        <td>
            <select class="form-select form-select-sm"
                    name="items[${id}][produk_satuan_id]"
                    id="satuan-${id}"
                    onchange="onSatuanChange(${id})"
                    disabled required>
                <option value="">-- Pilih Satuan --</option>
            </select>
            <div class="conversion-info" id="conversion-info-${id}"></div>
        </td>
        <td class="text-center">
            <small class="text-muted" id="konversi-${id}">× 1</small>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm text-end bg-light"
                   id="harga-${id}" readonly value="${fmt(hargaAwal)}">
            <input type="hidden" name="items[${id}][harga]"
                   id="harga-val-${id}" value="${hargaAwal}">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm"
                   name="items[${id}][qty_diminta]"
                   id="qty-${id}" min="1" value="${qty}"
                   onchange="calculateSubtotal(${id})" required>
            <small class="text-muted" id="max-qty-${id}"></small>
        </td>
        <td class="text-end">
            <strong id="subtotal-${id}">Rp ${fmt(hargaAwal * qty)}</strong>
            <input type="hidden" id="subtotal-val-${id}" value="${hargaAwal * qty}">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${id})">
                <i class="ri-delete-bin-line"></i>
            </button>
        </td>
    `;

    tbody.appendChild(row);
    initSelect2(id, produkId, produkSatuanId);
    return id;
}

function addItem() {
    if (filteredProduk.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Tidak Ada Produk', text: 'Tidak ada produk tersedia' });
        return;
    }
    const newIndex = document.querySelectorAll('#itemTableBody tr').length;
    createRow(newIndex);
}

// ──────────────────────────────────────────────────────────
// SELECT2 INIT
// ──────────────────────────────────────────────────────────
function initSelect2(itemId, selectedProdukId = null, selectedSatuanId = null) {
    const $sel = $(`#produk-${itemId}`);

    filteredProduk.forEach(p => {
        let text = '';
        if (isPenjualan) {
            text = `${p.nama} - ${p.merk || ''} (Stok: ${p.stock_gudang} PCS)`;
            if (p.no_batch !== '-') text += ` - Batch: ${p.no_batch}`;
        } else {
            text = `${p.nama} - ${p.merk || ''}`;
        }

        const opt = new Option(text, p.id, false, p.id === selectedProdukId);
        $(opt).data('produk', p);
        $sel.append(opt);
    });

    $sel.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Pilih Produk --',
        allowClear: true,
        templateResult: formatProdukOption,
        templateSelection: formatProdukSelected,
    });

    $sel.on('select2:select select2:unselect', () => onProdukChange(itemId));

    if (selectedProdukId) {
        onProdukChange(itemId, selectedSatuanId);
    }
}

function formatProdukOption(item) {
    if (!item.id) return item.text;
    const d = $(item.element).data('produk');
    if (!d) return item.text;

    if (isPenjualan) {
        return $(`<div>
            <strong>${d.nama}</strong> ${d.merk ? '- ' + d.merk : ''}
            <br>
            <small>
                <span class="badge bg-info stock-badge">Batch: ${d.no_batch || '-'}</span>
                <span class="badge bg-success stock-badge">Stok: ${d.stock_gudang} PCS</span>
                ${d.tanggal_kadaluarsa !== '-'
                    ? `<span class="badge bg-warning stock-badge text-dark">Exp: ${d.tanggal_kadaluarsa}</span>`
                    : ''}
            </small>
        </div>`);
    } else {
        return $(`<div>
            <strong>${d.nama}</strong> - ${d.merk || ''}
            <br>
            <small>
                <span class="badge bg-primary stock-badge">Harga: Rp ${fmt(d.harga_beli)}</span>
                <span class="badge bg-info stock-badge">Satuan: ${d.satuan}</span>
                <span class="badge bg-secondary stock-badge">Konversi: 1 : ${d.konversi || 1}</span>
            </small>
        </div>`);
    }
}

function formatProdukSelected(item) {
    if (!item.id) return item.text;
    const d = $(item.element).data('produk');
    return d ? d.nama : item.text;
}

// ──────────────────────────────────────────────────────────
// PRODUK CHANGE HANDLER
// ──────────────────────────────────────────────────────────
function onProdukChange(itemId, preSelectSatuanId = null) {
    const d = $(`#produk-${itemId}`).find(':selected').data('produk');

    const $satuan = $(`#satuan-${itemId}`);
    $satuan.html('<option value="">-- Pilih Satuan --</option>').prop('disabled', true);
    document.getElementById(`konversi-${itemId}`).textContent = '× 1';
    document.getElementById(`harga-${itemId}`).value = '0';
    document.getElementById(`harga-val-${itemId}`).value = '0';
    document.getElementById(`conversion-info-${itemId}`).textContent = '';
    document.getElementById(`batch-info-${itemId}`).innerHTML = '';
    document.getElementById(`max-qty-${itemId}`).textContent = '';
    calculateSubtotal(itemId);

    if (!d) return;

    if (isPenjualan) {
        document.getElementById(`batch-info-${itemId}`).innerHTML = `
            <span class="badge bg-info stock-badge">Batch: ${d.no_batch || '-'}</span>
            <span class="badge bg-success stock-badge">Stok: ${d.stock_gudang} PCS</span>
            ${d.tanggal_kadaluarsa !== '-'
                ? `<span class="badge bg-warning stock-badge text-dark">Exp: ${d.tanggal_kadaluarsa}</span>`
                : ''}
        `;
        document.getElementById(`max-qty-${itemId}`).textContent = `Maks: ${d.stock_gudang} PCS`;
        document.getElementById(`qty-${itemId}`).max = d.stock_gudang;
    }

    // Populate satuan options
    const satuans = d.satuans || [];
    if (satuans.length > 0) {
        satuans.forEach(s => {
            const harga = isPenjualan ? (s.harga_jual || 0) : (d.harga_beli || 0);
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = `${s.label} (1 : ${s.konversi} PCS)`;
            opt.dataset.harga = harga;
            opt.dataset.konversi = s.konversi;
            opt.dataset.label = s.label;
            $satuan.append(opt);
        });
        $satuan.prop('disabled', false);

        if (preSelectSatuanId) {
            $satuan.val(preSelectSatuanId);
            onSatuanChange(itemId);
        }
    } else {
        // Fallback jika tidak ada satuan
        const fallbackHarga = isPenjualan ? (d.harga_jual || 0) : (d.harga_beli || 0);
        document.getElementById(`harga-${itemId}`).value = fmt(fallbackHarga);
        document.getElementById(`harga-val-${itemId}`).value = fallbackHarga;
    }
}

// ──────────────────────────────────────────────────────────
// SATUAN CHANGE HANDLER
// ──────────────────────────────────────────────────────────
function onSatuanChange(itemId) {
    const sel = document.getElementById(`satuan-${itemId}`);
    const opt = sel.options[sel.selectedIndex];
    
    if (!opt || !opt.value) {
        document.getElementById(`konversi-${itemId}`).textContent = '× 1';
        document.getElementById(`conversion-info-${itemId}`).textContent = '';
        document.getElementById(`harga-${itemId}`).value = '0';
        document.getElementById(`harga-val-${itemId}`).value = '0';
        calculateSubtotal(itemId);
        return;
    }

    const harga = parseFloat(opt.dataset.harga || 0);
    const konversi = parseFloat(opt.dataset.konversi || 1);
    const label = opt.dataset.label || 'PCS';

    document.getElementById(`konversi-${itemId}`).textContent = `× ${konversi}`;
    document.getElementById(`conversion-info-${itemId}`).textContent = 
        `1 ${label} = ${konversi} PCS`;
    document.getElementById(`harga-${itemId}`).value = fmt(harga);
    document.getElementById(`harga-val-${itemId}`).value = harga;
    
    calculateSubtotal(itemId);
}

// ──────────────────────────────────────────────────────────
// CALCULATIONS
// ──────────────────────────────────────────────────────────
function calculateSubtotal(itemId) {
    const qty = parseInt(document.getElementById(`qty-${itemId}`)?.value || 0);
    const harga = parseFloat(document.getElementById(`harga-val-${itemId}`)?.value || 0);
    const sub = harga * qty;
    
    document.getElementById(`subtotal-${itemId}`).textContent = 'Rp ' + fmt(sub);
    document.getElementById(`subtotal-val-${itemId}`).value = sub;
    calculateTotal();
}

function calculateTotal() {
    let total = 0, totalPcs = 0, itemCount = 0, totalQty = 0;
    
    document.querySelectorAll('[id^="subtotal-val-"]').forEach(el => {
        total += parseFloat(el.value || 0);
        itemCount++;
        
        const id = el.id.split('-')[2];
        const qty = parseInt(document.getElementById(`qty-${id}`)?.value || 0);
        totalQty += qty;
        
        const konversiText = document.getElementById(`konversi-${id}`)?.textContent || '× 1';
        const konversi = parseInt(konversiText.replace('× ', '')) || 1;
        totalPcs += qty * konversi;
    });

    const grand = total + poPajak;
    
    document.getElementById('totalHarga').textContent = 'Rp ' + fmt(total);
    document.getElementById('grandTotal').textContent = 'Rp ' + fmt(grand);
    document.getElementById('summaryItemCount').textContent = itemCount;
    document.getElementById('summaryTotalQty').textContent = totalQty;
    document.getElementById('summaryTotalPcs').textContent = totalPcs + ' PCS';
    document.getElementById('summarySubtotal').textContent = 'Rp ' + fmt(total);
    document.getElementById('summaryGrandTotal').textContent = 'Rp ' + fmt(grand);
}

function removeItem(itemId) {
    if (document.querySelectorAll('#itemTableBody tr').length <= 1) {
        Swal.fire({ 
            icon: 'warning', 
            title: 'Tidak dapat menghapus', 
            text: 'Minimal harus ada 1 item' 
        });
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

function renumberRows() {
    document.querySelectorAll('#itemTableBody tr').forEach((row, i) => {
        row.querySelector('td:first-child').textContent = i + 1;
    });
    itemCounter = document.querySelectorAll('#itemTableBody tr').length;
}

function fmt(n) {
    return new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
}

// ──────────────────────────────────────────────────────────
// LOAD EXISTING ITEMS
// ──────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    existingItems.forEach((item, idx) => {
        const id = createRow(idx, item.id_produk, item.produk_satuan_id, item.qty_diminta, item.harga_satuan);
        
        // Set qty diminta satuan dasar jika ada
        if (item.qty_diminta_satuan_dasar) {
            const konversi = document.getElementById(`konversi-${id}`);
            if (konversi) {
                konversi.setAttribute('data-pcs', item.qty_diminta_satuan_dasar);
            }
        }
    });
    calculateTotal();
});

// ──────────────────────────────────────────────────────────
// FORM SUBMIT VALIDATION
// ──────────────────────────────────────────────────────────
document.getElementById('formPO').addEventListener('submit', function(e) {
    // Check items count
    if (document.querySelectorAll('#itemTableBody tr').length === 0) {
        e.preventDefault();
        Swal.fire({ 
            icon: 'warning', 
            title: 'Perhatian', 
            text: 'Tambahkan minimal 1 item' 
        });
        return;
    }
    
    // Check satuan for each item
    let missingSatuan = false;
    document.querySelectorAll('select[id^="satuan-"]').forEach(sel => {
        if (/^satuan-\d+$/.test(sel.id) && !sel.disabled && (!sel.value || sel.value === '')) {
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
        return;
    }
    
    // Check PIN
    const pin = document.getElementById('pin').value;
    if (!pin || pin.length !== 6 || !/^\d{6}$/.test(pin)) {
        e.preventDefault();
        Swal.fire({ 
            icon: 'warning', 
            title: 'PIN Tidak Valid', 
            text: 'PIN harus 6 digit angka' 
        });
        
        // Highlight PIN inputs
        document.querySelectorAll('.pin-input').forEach(input => {
            input.classList.add('error');
            setTimeout(() => input.classList.remove('error'), 500);
        });
        return;
    }
    
    // Show loading
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
});
</script>
@endpush