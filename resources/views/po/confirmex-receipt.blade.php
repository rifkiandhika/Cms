{{-- confirmex-receipt.blade.php --}}
@extends('layouts.app')

@section('title', 'Konfirmasi Penerimaan Barang dari Supplier')

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
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0"><i class="ri-file-list-3-line me-2"></i>Informasi Purchase Order</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td width="120"><strong>No. PO</strong></td><td>: {{ $po->no_po }}</td></tr>
                                    <tr><td><strong>Tanggal</strong></td><td>: {{ $po->tanggal_permintaan->format('d/m/Y H:i') }}</td></tr>
                                    <tr><td><strong>Pemohon</strong></td><td>: {{ $po->karyawanPemohon->nama_lengkap ?? '-' }}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td width="120"><strong>Supplier</strong></td><td>: <strong class="text-success">{{ $po->supplier->nama_supplier ?? '-' }}</strong></td></tr>
                                    <tr><td><strong>No. GR</strong></td><td>: <span class="badge bg-info">{{ $po->no_gr }}</span></td></tr>
                                    <tr><td><strong>Tgl Diterima</strong></td><td>: {{ $po->tanggal_diterima ? $po->tanggal_diterima->format('d/m/Y H:i') : '-' }}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Items --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="ri-checkbox-multiple-line me-2"></i>Pengecekan Barang dari Supplier</h5>
                        <small class="text-muted">Pilih gudang tujuan dan masukkan informasi batch untuk setiap item</small>
                    </div>
                    <div class="card-body p-0">
                        <div id="itemsContainer">
                            @foreach($po->items as $index => $item)
                            <div class="item-wrapper {{ !$loop->last ? 'border-bottom' : '' }}"
                                 data-item-index="{{ $index }}"
                                 data-konversi="{{ $item->konversi_snapshot }}">

                                {{-- ── Header item ── --}}
                                <div class="p-3 bg-light">
                                    <div class="row align-items-start g-2">
                                        {{-- Info produk --}}
                                        <div class="col-md-4">
                                            <div class="fw-bold">{{ $item->nama_produk }}</div>
                                            @if($item->produk)
                                                <small class="text-muted">{{ $item->produk->merk ?? '' }}</small>
                                            @endif
                                            <input type="hidden" name="items[{{ $index }}][id_po_item]" value="{{ $item->id_po_item }}">
                                        </div>

                                        {{-- Satuan & Qty --}}
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Satuan / Qty Diminta</small>
                                            @if($item->produkSatuan)
                                                <span class="badge bg-secondary">{{ $item->produkSatuan->satuan->nama_satuan ?? 'PCS' }}</span>
                                                <small class="text-muted">(× {{ $item->konversi_snapshot }} PCS)</small>
                                            @endif
                                            <div class="fw-bold text-primary mt-1">{{ number_format($item->qty_diminta) }} unit</div>
                                            <small class="text-muted">= {{ $item->qty_diminta_satuan_dasar }} PCS</small>
                                        </div>

                                        {{-- ── PILIH GUDANG PER ITEM (BARU) ── --}}
                                        <div class="col-md-5">
                                            <label class="form-label small fw-semibold mb-1">
                                                <i class="ri-store-2-line text-primary me-1"></i>
                                                Gudang Tujuan <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select form-select-sm item-gudang-select"
                                                    name="items[{{ $index }}][gudang_id]"
                                                    data-item-index="{{ $index }}"
                                                    required>
                                                <option value="">— Pilih Gudang —</option>
                                                @foreach($gudangs as $gudang)
                                                <option value="{{ $gudang->id }}"
                                                        data-kode="{{ $gudang->kode_gudang }}"
                                                        data-lokasi="{{ $gudang->lokasi ?? '' }}">
                                                    {{ $gudang->nama_gudang }}
                                                </option>
                                                @endforeach
                                            </select>
                                            {{-- Preview gudang yang dipilih --}}
                                            <div class="gudang-preview mt-1" id="gudangPreview-{{ $index }}" style="display:none">
                                                <div class="d-flex align-items-center gap-2 px-2 py-1 rounded"
                                                     style="background:#EEF1FF;border:1px solid #C7D2FE;font-size:.75rem">
                                                    <i class="ri-map-pin-line text-primary"></i>
                                                    <span class="gudang-lokasi-preview text-muted"></span>
                                                    <span class="badge ms-auto" style="background:#4F6EF7;font-size:.65rem"
                                                          id="gudangKode-{{ $index }}"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── Batch rows ── --}}
                                <div class="batch-container p-3" id="batchContainer-{{ $index }}">
                                    <div class="batch-row mb-2 p-3 border rounded bg-white" data-batch-index="0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0 text-muted small"><i class="ri-stack-line me-1"></i>Batch #1</h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-batch-btn py-0 px-2" style="display:none;font-size:.75rem">
                                                <i class="ri-delete-bin-line"></i> Hapus
                                            </button>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                <label class="form-label small mb-1">No. Batch</label>
                                                <input type="text" class="form-control form-control-sm"
                                                       name="items[{{ $index }}][batches][0][batch_number]"
                                                       placeholder="BATCH001">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small mb-1">Exp Date <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control form-control-sm"
                                                       name="items[{{ $index }}][batches][0][tanggal_kadaluarsa]" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small mb-1">Qty <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control form-control-sm qty-batch-input"
                                                       name="items[{{ $index }}][batches][0][qty_diterima]"
                                                       min="1" data-item-index="{{ $index }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small mb-1">Kondisi <span class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm kondisi-batch-select"
                                                        name="items[{{ $index }}][batches][0][kondisi]"
                                                        data-item-index="{{ $index }}" required>
                                                    <option value="Baik">Baik</option>
                                                    <option value="Rusak">Rusak</option>
                                                    <option value="Kadaluarsa">Kadaluarsa</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small mb-1">Catatan</label>
                                                <input type="text" class="form-control form-control-sm"
                                                       name="items[{{ $index }}][batches][0][catatan]"
                                                       placeholder="Opsional">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-3 pb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary add-batch-btn"
                                            data-item-index="{{ $index }}">
                                        <i class="ri-add-line"></i> Tambah Batch
                                    </button>
                                </div>

                                {{-- Summary per item --}}
                                <div class="px-3 pb-3">
                                    <div class="alert alert-info mb-0 small py-2">
                                        <strong>Total diterima:</strong>
                                        <span class="total-qty-item" data-item-index="{{ $index }}">0</span>
                                        dari {{ number_format($item->qty_diminta) }}
                                        (<span class="total-qty-pcs" data-item-index="{{ $index }}">0</span> PCS)
                                    </div>
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
                        <textarea class="form-control" name="catatan_penerima" rows="3"
                                  placeholder="Tambahkan catatan untuk penerimaan ini (opsional)..."></textarea>
                    </div>
                </div>
            </div>

            {{-- ══ RIGHT COLUMN ═════════════════════════════════════════ --}}
            <div class="col-xl-4">
                <div class="card shadow-sm border-0 mb-4 position-sticky" style="top:20px">

                    {{-- Ringkasan gudang per item --}}
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="ri-store-2-line me-2 text-primary"></i>Ringkasan Gudang Tujuan</h5>
                    </div>
                    <div class="card-body border-bottom p-0">
                        <div id="gudangSummaryList" style="max-height:200px;overflow-y:auto">
                            {{-- diisi JS --}}
                            <div class="text-center text-muted py-3 small" id="gudangSummaryEmpty">
                                <i class="ri-store-2-line opacity-25 d-block" style="font-size:1.5rem"></i>
                                Pilih gudang untuk setiap item
                            </div>
                        </div>
                    </div>

                    {{-- Ringkasan qty --}}
                    <div class="card-header bg-white py-3 border-top">
                        <h5 class="mb-0"><i class="ri-calculator-line me-2"></i>Ringkasan Penerimaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Item:</span>
                            <strong>{{ $po->items->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Batch:</span>
                            <strong id="totalBatch">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Qty Diminta:</span>
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
                    </div>

                    <div class="card-footer bg-white">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success btn-lg"
                                    id="btnSubmit" onclick="showPinModal()" disabled>
                                <i class="ri-check-double-line me-1"></i> Konfirmasi Penerimaan
                            </button>
                            <a href="{{ route('po.show', $po->id_po) }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Batal
                            </a>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-danger fw-semibold" id="btnHint"></small>
                        </div>
                    </div>
                </div>

                {{-- Panduan --}}
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="ri-information-line text-info me-2"></i>Panduan</h6>
                        <ul class="small mb-0">
                            <li class="mb-2">Pilih <strong>gudang tujuan</strong> untuk setiap obat</li>
                            <li class="mb-2">Satu PO bisa masuk ke <strong>gudang yang berbeda</strong></li>
                            <li class="mb-2">Klik <strong>"Tambah Batch"</strong> jika ada beberapa batch per item</li>
                            <li class="mb-2 text-success"><strong>Batch dengan no_batch + exp date sama akan otomatis digabung</strong></li>
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
                <h5 class="modal-title"><i class="ri-lock-password-line me-2"></i>Verifikasi PIN Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4 py-3">
                {{-- Ringkasan gudang tujuan --}}
                <div id="pinGudangSummary" class="alert alert-primary small text-start mb-3"></div>

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
                <button type="button" class="btn btn-success" id="confirmPinBtn" disabled>
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
.batch-row { transition: all .3s ease; }
.batch-row:hover { box-shadow: 0 2px 8px rgba(0,0,0,.08); }
.item-gudang-select:focus { border-color: #4F6EF7; box-shadow: 0 0 0 .2rem rgba(79,110,247,.2); }

/* Highlight item yang belum pilih gudang */
.item-wrapper.gudang-missing .item-gudang-select { border-color: #EF4444; }

.gudang-summary-row {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 14px; border-bottom: 1px solid #F1F3F9; font-size: .8rem;
}
.gudang-summary-row:last-child { border-bottom: none; }
.gudang-summary-dot {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
}

.otp-input {
    width: 48px; height: 58px; font-size: 22px; font-weight: bold;
    border: 2px solid #dee2e6; border-radius: 10px; transition: all .3s;
}
.otp-input:focus {
    border-color: #0d6efd; box-shadow: 0 0 0 .2rem rgba(13,110,253,.25);
    outline: none; transform: scale(1.05);
}
.otp-input.filled  { background: #f8f9fa; border-color: #198754; }
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
// ── Warna per gudang (assign saat dipilih) ───────────────────
const GUDANG_COLORS = [
    '#4F6EF7','#22C55E','#F59E0B','#EF4444','#8B5CF6',
    '#0891B2','#D97706','#16A34A','#9F1239','#6D28D9'
];
const gudangColorMap = {}; // gudang_id → color
let colorIdx = 0;

function getGudangColor(gudangId) {
    if (!gudangColorMap[gudangId]) {
        gudangColorMap[gudangId] = GUDANG_COLORS[colorIdx++ % GUDANG_COLORS.length];
    }
    return gudangColorMap[gudangId];
}

// ── Batch counters ───────────────────────────────────────────
let batchCounters = {};
document.querySelectorAll('.item-wrapper').forEach(el => {
    batchCounters[el.dataset.itemIndex] = 1;
});

// ── Gudang select per item ───────────────────────────────────
$(document).on('change', '.item-gudang-select', function () {
    const idx    = $(this).data('item-index');
    const val    = $(this).val();
    const opt    = $(this).find('option:selected');
    const lokasi = opt.data('lokasi') || '-';
    const kode   = opt.data('kode')   || '';

    if (val) {
        const color = getGudangColor(val);
        $(`#gudangPreview-${idx}`).show()
            .find('.gudang-lokasi-preview').text(lokasi || 'Tidak ada lokasi');
        $(`#gudangKode-${idx}`).text(kode).css('background', color);
        $(this).closest('.item-wrapper').removeClass('gudang-missing');
    } else {
        $(`#gudangPreview-${idx}`).hide();
    }

    updateGudangSummary();
    updateSubmitBtn();
});

// ── Ringkasan gudang di sidebar ──────────────────────────────
function updateGudangSummary() {
    const gudangMap = {}; // gudang_id → { nama, color, items: [] }

    document.querySelectorAll('.item-wrapper').forEach(wrapper => {
        const idx    = wrapper.dataset.itemIndex;
        const sel    = wrapper.querySelector('.item-gudang-select');
        const val    = sel?.value;
        const nama   = sel?.options[sel.selectedIndex]?.text?.trim();
        const produk = wrapper.querySelector('.fw-bold')?.textContent?.trim();

        if (val && nama && nama !== '— Pilih Gudang —') {
            if (!gudangMap[val]) {
                gudangMap[val] = { nama, color: getGudangColor(val), items: [] };
            }
            gudangMap[val].items.push(produk);
        }
    });

    const list  = document.getElementById('gudangSummaryList');
    const empty = document.getElementById('gudangSummaryEmpty');
    const keys  = Object.keys(gudangMap);

    if (!keys.length) {
        empty.style.display = '';
        // hapus rows lama (kecuali empty)
        list.querySelectorAll('.gudang-summary-row').forEach(r => r.remove());
        return;
    }

    empty.style.display = 'none';
    list.querySelectorAll('.gudang-summary-row').forEach(r => r.remove());

    keys.forEach(gid => {
        const g   = gudangMap[gid];
        const row = document.createElement('div');
        row.className = 'gudang-summary-row';
        row.innerHTML = `
            <div class="gudang-summary-dot" style="background:${g.color}"></div>
            <div class="flex-1">
                <div class="fw-semibold" style="color:${g.color}">${g.nama}</div>
                <div class="text-muted" style="font-size:.72rem">${g.items.join(', ')}</div>
            </div>
            <span class="badge rounded-pill" style="background:${g.color};opacity:.15;color:${g.color};border:1px solid ${g.color}">
                ${g.items.length} item
            </span>`;
        list.appendChild(row);
    });
}

// ── Tambah batch ─────────────────────────────────────────────
$(document).on('click', '.add-batch-btn', function () {
    const itemIndex = $(this).data('item-index');
    const batchIdx  = batchCounters[itemIndex];
    const container = $(`#batchContainer-${itemIndex}`);

    container.append(`
    <div class="batch-row mb-2 p-3 border rounded bg-white" data-batch-index="${batchIdx}">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0 text-muted small"><i class="ri-stack-line me-1"></i>Batch #${batchIdx + 1}</h6>
            <button type="button" class="btn btn-sm btn-danger remove-batch-btn py-0 px-2" style="font-size:.75rem">
                <i class="ri-delete-bin-line"></i> Hapus
            </button>
        </div>
        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label small mb-1">No. Batch</label>
                <input type="text" class="form-control form-control-sm"
                       name="items[${itemIndex}][batches][${batchIdx}][batch_number]" placeholder="BATCH001">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Exp Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-sm"
                       name="items[${itemIndex}][batches][${batchIdx}][tanggal_kadaluarsa]" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Qty <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-sm qty-batch-input"
                       name="items[${itemIndex}][batches][${batchIdx}][qty_diterima]"
                       min="1" data-item-index="${itemIndex}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Kondisi <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm kondisi-batch-select"
                        name="items[${itemIndex}][batches][${batchIdx}][kondisi]"
                        data-item-index="${itemIndex}" required>
                    <option value="Baik">Baik</option>
                    <option value="Rusak">Rusak</option>
                    <option value="Kadaluarsa">Kadaluarsa</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Catatan</label>
                <input type="text" class="form-control form-control-sm"
                       name="items[${itemIndex}][batches][${batchIdx}][catatan]" placeholder="Opsional">
            </div>
        </div>
    </div>`);

    batchCounters[itemIndex]++;
    container.find('.remove-batch-btn').show();
    calculateSummary();
});

// ── Hapus batch ──────────────────────────────────────────────
$(document).on('click', '.remove-batch-btn', function () {
    const batchRow  = $(this).closest('.batch-row');
    const itemIndex = $(this).closest('.item-wrapper').data('item-index');

    Swal.fire({
        title: 'Hapus Batch?', icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc3545', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
    }).then(r => {
        if (!r.isConfirmed) return;
        batchRow.remove();
        const container = $(`#batchContainer-${itemIndex}`);
        if (container.find('.batch-row').length === 1) {
            container.find('.remove-batch-btn').hide();
        }
        calculateSummary();
    });
});

// ── Hitung summary ───────────────────────────────────────────
$(document).on('input change', '.qty-batch-input, .kondisi-batch-select', calculateSummary);

function calculateSummary() {
    let totalBaik = 0, totalRusak = 0, totalDiterima = 0, totalBatch = 0, totalPcs = 0;

    document.querySelectorAll('.item-wrapper').forEach(wrapper => {
        const idx      = wrapper.dataset.itemIndex;
        const konversi = parseInt(wrapper.dataset.konversi) || 1;
        let itemTotal  = 0;

        wrapper.querySelectorAll('.batch-row').forEach(row => {
            totalBatch++;
            const qty     = parseInt(row.querySelector('.qty-batch-input')?.value) || 0;
            const kondisi = row.querySelector('.kondisi-batch-select')?.value;

            itemTotal     += qty;
            totalDiterima += qty;
            totalPcs      += qty * konversi;
            if (kondisi === 'Baik') totalBaik  += qty;
            else                    totalRusak  += qty;
        });

        const qtyEl = document.querySelector(`.total-qty-item[data-item-index="${idx}"]`);
        const pcsEl = document.querySelector(`.total-qty-pcs[data-item-index="${idx}"]`);
        if (qtyEl) qtyEl.textContent = itemTotal;
        if (pcsEl) pcsEl.textContent = itemTotal * konversi;
    });

    document.getElementById('totalBatch').textContent    = totalBatch;
    document.getElementById('totalBaik').textContent     = totalBaik;
    document.getElementById('totalRusak').textContent    = totalRusak;
    document.getElementById('totalDiterima').textContent = totalDiterima;
    document.getElementById('totalPcs').textContent      = totalPcs + ' PCS';

    updateSubmitBtn();
}

// ── Validasi & aktifkan tombol submit ────────────────────────
function updateSubmitBtn() {
    const items       = document.querySelectorAll('.item-wrapper');
    const totalQty    = parseInt(document.getElementById('totalDiterima').textContent) || 0;
    let   missingGudang = [];

    items.forEach(wrapper => {
        const sel   = wrapper.querySelector('.item-gudang-select');
        const nama  = wrapper.querySelector('.fw-bold')?.textContent?.trim() ?? `Item`;
        const hasQty = Array.from(wrapper.querySelectorAll('.qty-batch-input'))
                            .some(i => parseInt(i.value) > 0);

        // Tandai item yang ada qty tapi belum pilih gudang
        if (hasQty && (!sel || !sel.value)) {
            wrapper.classList.add('gudang-missing');
            missingGudang.push(nama.substring(0, 20));
        } else {
            wrapper.classList.remove('gudang-missing');
        }
    });

    const ok = totalQty > 0 && missingGudang.length === 0;
    document.getElementById('btnSubmit').disabled = !ok;

    const hint = document.getElementById('btnHint');
    if (totalQty === 0) {
        hint.textContent = 'Isi minimal 1 qty untuk melanjutkan';
    } else if (missingGudang.length > 0) {
        hint.textContent = `Pilih gudang untuk: ${missingGudang.join(', ')}`;
    } else {
        hint.textContent = '';
    }
}

calculateSummary();
updateGudangSummary();

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
    // Bangun ringkasan gudang untuk modal PIN
    const gudangMap = {};
    document.querySelectorAll('.item-wrapper').forEach(wrapper => {
        const sel   = wrapper.querySelector('.item-gudang-select');
        const val   = sel?.value;
        const nama  = sel?.options[sel.selectedIndex]?.text?.trim();
        const color = val ? getGudangColor(val) : '#888';
        if (val) {
            if (!gudangMap[val]) gudangMap[val] = { nama, color, count: 0 };
            gudangMap[val].count++;
        }
    });

    const summaryHtml = Object.values(gudangMap).map(g =>
        `<div class="d-flex align-items-center gap-2 mb-1">
            <div style="width:10px;height:10px;border-radius:50%;background:${g.color};flex-shrink:0"></div>
            <strong style="color:${g.color}">${g.nama}</strong>
            <span class="ms-auto text-muted">${g.count} item</span>
         </div>`
    ).join('');

    document.getElementById('pinGudangSummary').innerHTML =
        `<div class="fw-semibold mb-2"><i class="ri-store-2-line me-1"></i>Stok akan masuk ke:</div>` + summaryHtml;

    resetPinInput();
    pinModal.show();
}

confirmPinBtn.addEventListener('click', function () {
    const pin      = Array.from(otpInputs).map(i => i.value).join('');
    const formData = new FormData(document.getElementById('formConfirm'));
    formData.set('pin', pin);

    // Bangun teks konfirmasi
    const gudangMap = {};
    document.querySelectorAll('.item-wrapper').forEach(wrapper => {
        const sel  = wrapper.querySelector('.item-gudang-select');
        const val  = sel?.value;
        const nama = sel?.options[sel.selectedIndex]?.text?.trim();
        if (val) {
            if (!gudangMap[val]) gudangMap[val] = { nama, color: getGudangColor(val), count: 0 };
            gudangMap[val].count++;
        }
    });

    const gudangRows = Object.values(gudangMap).map(g =>
        `<p class="mb-1"><span style="color:${g.color}">●</span> <strong>${g.nama}</strong> — ${g.count} item</p>`
    ).join('');

    Swal.fire({
        title: 'Konfirmasi Penerimaan',
        html: `<div class="text-start">
            <div class="mb-2 fw-semibold">Gudang Tujuan:</div>
            ${gudangRows}
            <hr>
            <p class="mb-1">Total Batch: <strong>${document.getElementById('totalBatch').textContent}</strong></p>
            <p class="mb-1">Total Diterima: <strong>${document.getElementById('totalDiterima').textContent} unit</strong></p>
            <p class="mb-1">Total PCS: <strong>${document.getElementById('totalPcs').textContent}</strong></p>
            <p class="mb-1">Kondisi Baik: <strong class="text-success">${document.getElementById('totalBaik').textContent}</strong></p>
            <p class="mb-0">Rusak/Expired: <strong class="text-danger">${document.getElementById('totalRusak').textContent}</strong></p>
        </div>`,
        icon: 'question', showCancelButton: true,
        confirmButtonText: 'Ya, Konfirmasi!', cancelButtonText: 'Batal'
    }).then(result => {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        fetch("{{ route('po.confirmex-receipt', $po->id_po) }}", {
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
                throw new Error(res.error || 'Gagal');
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