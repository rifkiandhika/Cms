{{-- confirm-receipt.blade.php --}}
@extends('layouts.app')

@section('title', 'Konfirmasi Penerimaan Barang oleh Customer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('po.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item"><a href="{{ route('po.show', $po->id_po) }}">Detail PO</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Konfirmasi Penerimaan</li>
@endsection

@section('content')
<div class="app-body">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form id="formConfirm">
        @csrf
        <div class="row">

            {{-- ══ LEFT COLUMN ══════════════════════════════════════════ --}}
            <div class="col-xl-8">

                {{-- PO Info --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-warning py-3">
                        <h5 class="mb-0 text-dark"><i class="ri-file-list-3-line me-2"></i>Informasi Purchase Order</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="120"><strong>No. PO</strong></td>
                                        <td>: {{ $po->no_po }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal</strong></td>
                                        <td>: {{ $po->tanggal_permintaan->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pemohon</strong></td>
                                        <td>: {{ $po->karyawanPemohon->nama_lengkap ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="120"><strong>Customer</strong></td>
                                        <td>: <strong class="text-warning">{{ $po->customer->nama_customer ?? '-' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>No. GR</strong></td>
                                        <td>: <span class="badge bg-warning text-dark">{{ $po->no_gr }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Disetujui</strong></td>
                                        <td>: {{ $po->kepalaGudang->nama_lengkap ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Items --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="ri-checkbox-multiple-line me-2"></i>Pengecekan Barang Diterima Customer</h5>
                        <small class="text-muted">Klik "Tambah Kondisi" jika ada sebagian barang dalam kondisi berbeda</small>
                    </div>
                    <div class="card-body p-0">
                        <div id="itemsContainer">
                            @foreach($po->items as $index => $item)
                            <div class="item-wrapper {{ !$loop->last ? 'border-bottom' : '' }}"
                                 data-item-index="{{ $index }}"
                                 data-konversi="{{ $item->konversi_snapshot }}">

                                {{-- ── Header item ── --}}
                                <div class="p-3 bg-light">
                                    <div class="row align-items-center g-2">
                                        {{-- Info produk --}}
                                        <div class="col-md-5">
                                            <div class="fw-bold">{{ $item->nama_produk }}</div>
                                            @if($item->produk)
                                                <small class="text-muted">{{ $item->produk->merk ?? '' }}</small>
                                            @endif
                                            <input type="hidden" name="items[{{ $index }}][id_po_item]" value="{{ $item->id_po_item }}">
                                        </div>

                                        {{-- Satuan & Qty --}}
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Satuan / Qty Dipesan</small>
                                            @if($item->produkSatuan)
                                                <span class="badge bg-secondary">{{ $item->produkSatuan->satuan->nama_satuan ?? 'PCS' }}</span>
                                                <small class="text-muted">(× {{ $item->konversi_snapshot }} PCS)</small>
                                            @endif
                                            <div class="fw-bold text-warning mt-1">{{ number_format($item->qty_diminta) }} unit</div>
                                            <small class="text-muted">= {{ $item->qty_diminta_satuan_dasar }} PCS</small>
                                        </div>

                                        {{-- Summary per item --}}
                                        <div class="col-md-3 text-end">
                                            <small class="text-muted d-block">Total dikonfirmasi</small>
                                            <span class="fw-bold text-primary">
                                                <span class="total-qty-item" data-item-index="{{ $index }}">0</span>
                                                / {{ number_format($item->qty_diminta) }}
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                (<span class="total-qty-pcs" data-item-index="{{ $index }}">0</span> PCS)
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── Kondisi rows ── --}}
                                <div class="kondisi-container p-3" id="kondisiContainer-{{ $index }}">
                                    <div class="kondisi-row mb-2 p-3 border rounded bg-white" data-kondisi-index="0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0 text-muted small">
                                                <i class="ri-checkbox-circle-line me-1"></i>Kondisi #1
                                            </h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-kondisi-btn py-0 px-2"
                                                    style="display:none;font-size:.75rem">
                                                <i class="ri-delete-bin-line"></i> Hapus
                                            </button>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <label class="form-label small mb-1">
                                                    Qty Diterima <span class="text-danger">*</span>
                                                </label>
                                                <input type="number"
                                                       class="form-control form-control-sm qty-kondisi-input"
                                                       name="items[{{ $index }}][kondisi_rows][0][qty_diterima]"
                                                       min="1"
                                                       max="{{ $item->qty_diminta }}"
                                                       data-item-index="{{ $index }}"
                                                       placeholder="0"
                                                       required>
                                                <small class="text-muted">Max: {{ $item->qty_diminta }}</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small mb-1">
                                                    Kondisi <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select form-select-sm kondisi-select"
                                                        name="items[{{ $index }}][kondisi_rows][0][kondisi]"
                                                        data-item-index="{{ $index }}"
                                                        required>
                                                    <option value="baik">✓ Baik</option>
                                                    <option value="rusak">✗ Rusak</option>
                                                    <option value="kadaluarsa">⚠ Kadaluarsa</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small mb-1">Catatan</label>
                                                <input type="text"
                                                       class="form-control form-control-sm"
                                                       name="items[{{ $index }}][kondisi_rows][0][catatan]"
                                                       placeholder="Opsional">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-3 pb-3">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-warning add-kondisi-btn"
                                            data-item-index="{{ $index }}">
                                        <i class="ri-add-line"></i> Tambah Kondisi
                                    </button>
                                    <small class="text-muted ms-2">
                                        Gunakan ini jika ada sebagian barang rusak atau kadaluarsa
                                    </small>
                                </div>

                            </div>{{-- .item-wrapper --}}
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="ri-chat-3-line me-2"></i>Catatan Tambahan</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control"
                                  name="catatan_penerima"
                                  rows="3"
                                  placeholder="Tambahkan catatan untuk penerimaan ini (opsional)..."></textarea>
                    </div>
                </div>
            </div>

            {{-- ══ RIGHT COLUMN ═════════════════════════════════════════ --}}
            <div class="col-xl-4">
                <div class="card shadow-sm border-0 mb-4 position-sticky" style="top:20px">

                    {{-- Ringkasan --}}
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="ri-calculator-line me-2"></i>Ringkasan Penerimaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Item:</span>
                            <strong>{{ $po->items->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Baris Kondisi:</span>
                            <strong id="totalKondisiRows">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Qty Dipesan:</span>
                            <strong>{{ $po->items->sum('qty_diminta') }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-success">Qty Baik:</span>
                            <strong id="totalBaik" class="text-success">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-danger">Qty Rusak/Expired:</span>
                            <strong id="totalRusak" class="text-danger">0</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total Diterima:</span>
                            <h5 class="text-primary mb-0" id="totalDiterima">0</h5>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <span class="text-muted">Total PCS:</span>
                            <strong id="totalPcs">0 PCS</strong>
                        </div>

                        <div class="alert alert-warning mt-3 small mb-0">
                            <i class="ri-alert-line me-1"></i>
                            <strong>Perhatian:</strong> Stok gudang akan <strong>dikurangi otomatis</strong>
                            sesuai qty yang dikonfirmasi (FIFO).
                        </div>
                    </div>

                    <div class="card-footer bg-white">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-warning btn-lg text-dark"
                                    id="btnSubmit" onclick="showPinModal()" disabled>
                                <i class="ri-check-double-line me-1"></i> Konfirmasi Penerimaan
                            </button>
                            <a href="{{ route('po.show', $po->id_po) }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Batal
                            </a>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-danger fw-semibold" id="btnHint">
                                Isi minimal 1 qty untuk melanjutkan
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Panduan --}}
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="ri-information-line text-info me-2"></i>Panduan Pengecekan
                        </h6>
                        <ul class="small mb-0">
                            <li class="mb-2">✓ Masukkan qty sesuai kondisi barang</li>
                            <li class="mb-2">✓ Klik <strong>"Tambah Kondisi"</strong> jika ada sebagian barang rusak</li>
                            <li class="mb-2">✓ Contoh: 10 Baik + 10 Rusak = 2 baris kondisi</li>
                            <li class="mb-2 text-warning">
                                <strong>⚠ Stok gudang otomatis berkurang (FIFO)</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

{{-- PIN Modal --}}
<div class="modal fade" id="pinModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="ri-lock-password-line me-2"></i>Verifikasi PIN Karyawan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4 py-3">
                {{-- Ringkasan sebelum konfirmasi --}}
                <div id="pinSummary" class="alert alert-warning small text-start mb-3"></div>

                <p class="text-muted mb-3">Masukkan PIN 6 digit untuk konfirmasi penerimaan barang</p>
                <div class="otp-container d-flex justify-content-center gap-2 mb-3">
                    @for($i = 0; $i < 6; $i++)
                    <input type="password" class="otp-input form-control text-center"
                           maxlength="1" pattern="\d" inputmode="numeric"
                           data-index="{{ $i }}" autocomplete="off">
                    @endfor
                </div>
                <div class="alert alert-info mb-0 small">
                    <i class="ri-information-line me-1"></i>
                    PIN digunakan untuk mencatat karyawan yang memproses penerimaan
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning text-dark" id="confirmPinBtn" disabled>
                    <i class="ri-check-line me-1"></i>Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.position-sticky { position: sticky; z-index: 10; }
.item-wrapper { transition: background .15s; }
.kondisi-row { transition: all .3s ease; }
.kondisi-row:hover { box-shadow: 0 2px 8px rgba(0,0,0,.08); }

.otp-input {
    width: 48px; height: 58px; font-size: 22px; font-weight: bold;
    border: 2px solid #dee2e6; border-radius: 10px; transition: all .3s;
}
.otp-input:focus {
    border-color: #ffc107; box-shadow: 0 0 0 .2rem rgba(255,193,7,.25);
    outline: none; transform: scale(1.05);
}
.otp-input.filled  { background: #fff9e6; border-color: #ffc107; }
.otp-input.error   { border-color: #dc3545; animation: shake .5s; }

@keyframes shake {
    0%,100% { transform: translateX(0); }
    25%      { transform: translateX(-5px); }
    75%      { transform: translateX(5px); }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ── Kondisi row counters ─────────────────────────────────────
let kondisiCounters = {};
document.querySelectorAll('.item-wrapper').forEach(el => {
    kondisiCounters[el.dataset.itemIndex] = 1;
});

// ── Tambah baris kondisi ─────────────────────────────────────
$(document).on('click', '.add-kondisi-btn', function () {
    const itemIndex  = $(this).data('item-index');
    const kondisiIdx = kondisiCounters[itemIndex];
    const container  = $(`#kondisiContainer-${itemIndex}`);
    const wrapper    = $(this).closest('.item-wrapper');
    const maxQty     = parseInt(wrapper.find('.total-qty-item').closest('.col-md-3').prev().prev().find('.fw-bold.text-warning').text().replace(/,/g, '')) || 9999;

    container.append(`
    <div class="kondisi-row mb-2 p-3 border rounded bg-white" data-kondisi-index="${kondisiIdx}">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0 text-muted small">
                <i class="ri-checkbox-circle-line me-1"></i>Kondisi #${kondisiIdx + 1}
            </h6>
            <button type="button" class="btn btn-sm btn-danger remove-kondisi-btn py-0 px-2" style="font-size:.75rem">
                <i class="ri-delete-bin-line"></i> Hapus
            </button>
        </div>
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label small mb-1">Qty Diterima <span class="text-danger">*</span></label>
                <input type="number"
                       class="form-control form-control-sm qty-kondisi-input"
                       name="items[${itemIndex}][kondisi_rows][${kondisiIdx}][qty_diterima]"
                       min="1"
                       data-item-index="${itemIndex}"
                       placeholder="0"
                       required>
            </div>
            <div class="col-md-4">
                <label class="form-label small mb-1">Kondisi <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm kondisi-select"
                        name="items[${itemIndex}][kondisi_rows][${kondisiIdx}][kondisi]"
                        data-item-index="${itemIndex}"
                        required>
                    <option value="baik">✓ Baik</option>
                    <option value="rusak">✗ Rusak</option>
                    <option value="kadaluarsa">⚠ Kadaluarsa</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small mb-1">Catatan</label>
                <input type="text"
                       class="form-control form-control-sm"
                       name="items[${itemIndex}][kondisi_rows][${kondisiIdx}][catatan]"
                       placeholder="Opsional">
            </div>
        </div>
    </div>`);

    kondisiCounters[itemIndex]++;
    container.find('.remove-kondisi-btn').show();
    calculateSummary();
});

// ── Hapus baris kondisi ──────────────────────────────────────
$(document).on('click', '.remove-kondisi-btn', function () {
    const kondisiRow = $(this).closest('.kondisi-row');
    const itemIndex  = $(this).closest('.item-wrapper').data('item-index');

    Swal.fire({
        title: 'Hapus baris kondisi ini?', icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc3545', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
    }).then(r => {
        if (!r.isConfirmed) return;
        kondisiRow.remove();
        const container = $(`#kondisiContainer-${itemIndex}`);
        if (container.find('.kondisi-row').length === 1) {
            container.find('.remove-kondisi-btn').hide();
        }
        calculateSummary();
    });
});

// ── Hitung summary ───────────────────────────────────────────
$(document).on('input change', '.qty-kondisi-input, .kondisi-select', calculateSummary);

function calculateSummary() {
    let totalBaik      = 0;
    let totalRusak     = 0;
    let totalDiterima  = 0;
    let totalKondisiRows = 0;
    let totalPcs       = 0;

    document.querySelectorAll('.item-wrapper').forEach(wrapper => {
        const idx      = wrapper.dataset.itemIndex;
        const konversi = parseInt(wrapper.dataset.konversi) || 1;
        let   itemTotal = 0;

        wrapper.querySelectorAll('.kondisi-row').forEach(row => {
            totalKondisiRows++;
            const qty     = parseInt(row.querySelector('.qty-kondisi-input')?.value) || 0;
            const kondisi = row.querySelector('.kondisi-select')?.value;

            itemTotal     += qty;
            totalDiterima += qty;
            totalPcs      += qty * konversi;

            if (kondisi === 'baik') totalBaik  += qty;
            else                    totalRusak  += qty;
        });

        const qtyEl = document.querySelector(`.total-qty-item[data-item-index="${idx}"]`);
        const pcsEl = document.querySelector(`.total-qty-pcs[data-item-index="${idx}"]`);
        if (qtyEl) qtyEl.textContent = itemTotal;
        if (pcsEl) pcsEl.textContent = itemTotal * konversi;
    });

    document.getElementById('totalKondisiRows').textContent = totalKondisiRows;
    document.getElementById('totalBaik').textContent        = totalBaik;
    document.getElementById('totalRusak').textContent       = totalRusak;
    document.getElementById('totalDiterima').textContent    = totalDiterima;
    document.getElementById('totalPcs').textContent         = totalPcs + ' PCS';

    updateSubmitBtn(totalDiterima);
}

function updateSubmitBtn(totalDiterima) {
    const ok   = totalDiterima > 0;
    const btn  = document.getElementById('btnSubmit');
    const hint = document.getElementById('btnHint');

    btn.disabled  = !ok;
    hint.textContent = ok ? '' : 'Isi minimal 1 qty untuk melanjutkan';
}

calculateSummary();

// ── PIN OTP ──────────────────────────────────────────────────
const otpInputs     = document.querySelectorAll('.otp-input');
const confirmPinBtn = document.getElementById('confirmPinBtn');
const pinModal      = new bootstrap.Modal(document.getElementById('pinModal'));

otpInputs.forEach((input, index) => {
    input.addEventListener('input', e => {
        if (!/^\d$/.test(e.target.value)) { e.target.value = ''; return; }
        input.classList.add('filled');
        if (index < otpInputs.length - 1) otpInputs[index + 1].focus();
        confirmPinBtn.disabled = !isPinComplete();
    });
    input.addEventListener('keydown', e => {
        if (e.key === 'Backspace') {
            if (!e.target.value && index > 0) {
                otpInputs[index - 1].focus();
                otpInputs[index - 1].value = '';
                otpInputs[index - 1].classList.remove('filled');
            }
            confirmPinBtn.disabled = !isPinComplete();
        } else if (e.key === 'Enter' && isPinComplete()) {
            confirmPinBtn.click();
        }
    });
    input.addEventListener('paste', e => {
        e.preventDefault();
        const pasted = e.clipboardData.getData('text').trim();
        if (/^\d{6}$/.test(pasted)) {
            pasted.split('').forEach((c, i) => {
                if (otpInputs[i]) { otpInputs[i].value = c; otpInputs[i].classList.add('filled'); }
            });
            otpInputs[5].focus();
            confirmPinBtn.disabled = !isPinComplete();
        }
    });
});

function isPinComplete() {
    return Array.from(otpInputs).every(i => i.value !== '');
}

function resetPinInput() {
    otpInputs.forEach(i => { i.value = ''; i.classList.remove('filled', 'error'); });
    if (otpInputs[0]) otpInputs[0].focus();
    confirmPinBtn.disabled = true;
}

function showPinModal() {
    // Bangun ringkasan untuk modal PIN
    const totalDiterima = document.getElementById('totalDiterima').textContent;
    const totalBaik     = document.getElementById('totalBaik').textContent;
    const totalRusak    = document.getElementById('totalRusak').textContent;
    const totalPcs      = document.getElementById('totalPcs').textContent;

    document.getElementById('pinSummary').innerHTML = `
        <div class="fw-semibold mb-2">
            <i class="ri-inbox-line me-1"></i>Ringkasan Konfirmasi:
        </div>
        <div class="d-flex justify-content-between"><span>Total Diterima:</span><strong>${totalDiterima} unit</strong></div>
        <div class="d-flex justify-content-between"><span>Total PCS:</span><strong>${totalPcs}</strong></div>
        <div class="d-flex justify-content-between text-success"><span>Kondisi Baik:</span><strong>${totalBaik}</strong></div>
        <div class="d-flex justify-content-between text-danger"><span>Rusak/Expired:</span><strong>${totalRusak}</strong></div>
    `;

    resetPinInput();
    pinModal.show();
}

// ── Submit ───────────────────────────────────────────────────
confirmPinBtn.addEventListener('click', function () {
    const pin      = Array.from(otpInputs).map(i => i.value).join('');
    const formData = new FormData(document.getElementById('formConfirm'));
    formData.set('pin', pin);

    const totalDiterima = document.getElementById('totalDiterima').textContent;
    const totalBaik     = document.getElementById('totalBaik').textContent;
    const totalRusak    = document.getElementById('totalRusak').textContent;
    const totalPcs      = document.getElementById('totalPcs').textContent;

    Swal.fire({
        title: 'Konfirmasi Penerimaan Customer',
        html: `<div class="text-start">
            <p class="mb-1">Total Diterima: <strong>${totalDiterima} unit</strong></p>
            <p class="mb-1">Total PCS: <strong>${totalPcs}</strong></p>
            <p class="mb-1">Kondisi Baik: <strong class="text-success">${totalBaik}</strong></p>
            <p class="mb-1">Rusak/Expired: <strong class="text-danger">${totalRusak}</strong></p>
            <div class="alert alert-warning mt-2 mb-0 small">
                <strong>⚠ Stok gudang akan dikurangi otomatis!</strong>
            </div>
        </div>`,
        icon: 'question', showCancelButton: true,
        confirmButtonText: 'Ya, Konfirmasi!',
        confirmButtonColor: '#ffc107',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        fetch("{{ route('po.confirm-receipt.store', $po->id_po) }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData,
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message })
                    .then(() => window.location.href = "{{ route('po.show', $po->id_po) }}");
            } else {
                throw new Error(res.error || 'Gagal konfirmasi');
            }
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Gagal', text: err.message });
            resetPinInput();
        });
    });
});
</script>
@endpush