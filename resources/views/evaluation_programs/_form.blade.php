@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Terdapat kesalahan!</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Program Header Information --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="ri-information-line me-2"></i>Informasi Program Evaluasi</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Judul Program <span class="text-danger">*</span></label>
                
                <input type="hidden" name="sop_id" value="{{ old('sop_id', request('sop_id') ?? $evaluationProgram->sop_id ?? '') }}">
                <input type="text" name="title" class="form-control"
                       value="{{ old('title', $evaluationProgram->title ?? '') }}"
                       placeholder="e.g. Evaluasi Efektivitas Pelatihan K3"
                       required>
                <small class="text-muted">Nama/judul program evaluasi</small>
            </div>

            <div class="col-md-3">
                <label class="form-label">No. Program <span class="text-danger">*</span></label>
                <input type="text" name="program_number" class="form-control"
                       value="{{ old('program_number', $evaluationProgram->program_number ?? '') }}"
                       placeholder="e.g. EVAL-001"
                       required>
                <small class="text-muted">Nomor unik program</small>
            </div>

            <div class="col-md-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="draft"     {{ old('status', $evaluationProgram->status ?? 'draft')    == 'draft'    ? 'selected' : '' }}>Draft</option>
                    <option value="active"    {{ old('status', $evaluationProgram->status ?? '')         == 'active'   ? 'selected' : '' }}>Aktif</option>
                    <option value="archived"  {{ old('status', $evaluationProgram->status ?? '')         == 'archived' ? 'selected' : '' }}>Arsip</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Materi Pelatihan <span class="text-danger">*</span></label>
                <input type="text" name="materi_pelatihan" class="form-control"
                       value="{{ old('materi_pelatihan', $evaluationProgram->materi_pelatihan ?? '') }}"
                       placeholder="e.g. Pelatihan Keselamatan Kerja"
                       required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Hari/Tanggal</label>
                <input type="date" name="hari_tanggal" class="form-control"
                       value="{{ old('hari_tanggal', isset($evaluationProgram->hari_tanggal) ? $evaluationProgram->hari_tanggal->format('Y-m-d') : '') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Tempat Pelatihan</label>
                <input type="text" name="tempat_pelatihan" class="form-control"
                       value="{{ old('tempat_pelatihan', $evaluationProgram->tempat_pelatihan ?? '') }}"
                       placeholder="e.g. Ruang Seminar">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="2"
                          placeholder="Deskripsi singkat program evaluasi (opsional)">{{ old('description', $evaluationProgram->description ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- Items Evaluasi --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="ri-list-check me-2"></i>Item Evaluasi
        </h5>
        <button type="button" class="btn btn-light btn-sm" id="addItem">
            <i class="ri-add-circle-line me-1"></i>Tambah Item
        </button>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-3">
            <i class="ri-information-line me-2"></i>
            <strong>Panduan:</strong> Tambahkan item evaluasi dengan label huruf (A, B, C, D, ...) dan isi konten/pertanyaannya.
            Sesuai form evaluasi, biasanya berisi: Kompetensi yang diharapkan, Perilaku sebelum training, Perilaku setelah training, Pendapat efektivitas training.
        </div>

        <div id="itemsContainer">
            @php
                $existingItems = isset($evaluationProgram) ? $evaluationProgram->items : collect();
                $oldItems      = old('items', []);
            @endphp

            @if($existingItems->count() > 0)
                @foreach($existingItems as $idx => $item)
                    <div class="item-row border rounded p-3 mb-3" data-index="{{ $idx }}">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="fw-bold text-primary">
                                <i class="ri-survey-line me-1"></i>Item #<span class="item-number">{{ $idx + 1 }}</span>
                            </span>
                            <button type="button" class="btn btn-danger btn-sm remove-item">
                                <i class="ri-delete-bin-line me-1"></i>Hapus
                            </button>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-1">
                                <label class="form-label small fw-bold">Label <span class="text-danger">*</span></label>
                                <input type="text" name="items[{{ $idx }}][item_label]"
                                       class="form-control form-control-sm text-center fw-bold"
                                       value="{{ old("items.$idx.item_label", $item->item_label) }}"
                                       placeholder="A" maxlength="10" required>
                                <small class="text-muted">A/B/C/D</small>
                            </div>
                            <div class="col-md-10">
                                <label class="form-label small fw-bold">Konten / Pertanyaan <span class="text-danger">*</span></label>
                                <textarea name="items[{{ $idx }}][item_content]"
                                          class="form-control form-control-sm"
                                          rows="2"
                                          placeholder="Tulis pertanyaan atau konten evaluasi..." required>{{ old("items.$idx.item_content", $item->item_content) }}</textarea>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small fw-bold">Urutan</label>
                                <input type="number" name="items[{{ $idx }}][order]"
                                       class="form-control form-control-sm"
                                       value="{{ old("items.$idx.order", $item->order) }}"
                                       min="0" required>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div id="emptyItemsMsg" class="text-center text-muted py-4">
                    <i class="ri-list-check" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">Belum ada item evaluasi. Klik <strong>"Tambah Item"</strong> untuk memulai.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Peserta --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="ri-group-line me-2"></i>Daftar Peserta</h5>
        <button type="button" class="btn btn-light btn-sm" id="addParticipant">
            <i class="ri-user-add-line me-1"></i>Tambah Peserta
        </button>
    </div>
    <div class="card-body">
        <div id="participantsContainer">
            @php
                $existingParticipants = isset($evaluationProgram) ? $evaluationProgram->participants : collect();
            @endphp

            @if($existingParticipants->count() > 0)
                @foreach($existingParticipants as $pIdx => $participant)
                    <div class="participant-row border rounded p-2 mb-2">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-1 text-center">
                                <span class="badge bg-info participant-number">{{ $pIdx + 1 }}</span>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="participants[{{ $pIdx }}][nama_peserta]"
                                       class="form-control form-control-sm"
                                       value="{{ old("participants.$pIdx.nama_peserta", $participant->nama_peserta) }}"
                                       placeholder="Nama Peserta" required>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="participants[{{ $pIdx }}][jabatan_lokasi_kerja]"
                                       class="form-control form-control-sm"
                                       value="{{ old("participants.$pIdx.jabatan_lokasi_kerja", $participant->jabatan_lokasi_kerja) }}"
                                       placeholder="Jabatan / Lokasi Kerja">
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-danger btn-sm remove-participant">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div id="emptyParticipantsMsg" class="text-center text-muted py-3">
                    <i class="ri-group-line" style="font-size: 1.5rem;"></i>
                    <p class="mt-1 mb-0 small">Belum ada peserta. Klik <strong>"Tambah Peserta"</strong>.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// ── Item counter (mulai dari jumlah item existing) ──────────────────────────
let itemIndex = {{ isset($evaluationProgram) ? $evaluationProgram->items->count() : 0 }};
let participantIndex = {{ isset($evaluationProgram) ? $evaluationProgram->participants->count() : 0 }};

// Label suggestions berurutan
const labelSuggestions = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

function getNextLabel() {
    const usedLabels = [];
    $('.item-row').each(function () {
        usedLabels.push($(this).find('input[name*="item_label"]').val().toUpperCase());
    });
    for (const lbl of labelSuggestions) {
        if (!usedLabels.includes(lbl)) return lbl;
    }
    return String.fromCharCode(65 + itemIndex); // fallback A, B, C...
}

// ── Template Item ────────────────────────────────────────────────────────────
function getItemTemplate(index) {
    const label = getNextLabel();
    return `
        <div class="item-row border rounded p-3 mb-3" data-index="${index}">
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <span class="fw-bold text-primary">
                    <i class="ri-survey-line me-1"></i>Item #<span class="item-number">${$('.item-row').length + 1}</span>
                </span>
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    <i class="ri-delete-bin-line me-1"></i>Hapus
                </button>
            </div>
            <div class="row g-2">
                <div class="col-md-1">
                    <label class="form-label small fw-bold">Label <span class="text-danger">*</span></label>
                    <input type="text" name="items[${index}][item_label]"
                           class="form-control form-control-sm text-center fw-bold"
                           value="${label}" placeholder="A" maxlength="10" required>
                    <small class="text-muted">A/B/C/D</small>
                </div>
                <div class="col-md-10">
                    <label class="form-label small fw-bold">Konten / Pertanyaan <span class="text-danger">*</span></label>
                    <textarea name="items[${index}][item_content]"
                              class="form-control form-control-sm"
                              rows="2"
                              placeholder="Tulis pertanyaan atau konten evaluasi..." required></textarea>
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-bold">Urutan</label>
                    <input type="number" name="items[${index}][order]"
                           class="form-control form-control-sm"
                           value="${$('.item-row').length + 1}" min="0" required>
                </div>
            </div>
        </div>
    `;
}

// ── Template Peserta ─────────────────────────────────────────────────────────
function getParticipantTemplate(index) {
    const num = $('.participant-row').length + 1;
    return `
        <div class="participant-row border rounded p-2 mb-2">
            <div class="row g-2 align-items-center">
                <div class="col-md-1 text-center">
                    <span class="badge bg-info participant-number">${num}</span>
                </div>
                <div class="col-md-5">
                    <input type="text" name="participants[${index}][nama_peserta]"
                           class="form-control form-control-sm"
                           placeholder="Nama Peserta" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="participants[${index}][jabatan_lokasi_kerja]"
                           class="form-control form-control-sm"
                           placeholder="Jabatan / Lokasi Kerja">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-danger btn-sm remove-participant">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

// ── Add Item ─────────────────────────────────────────────────────────────────
$('#addItem').on('click', function () {
    $('#emptyItemsMsg').remove();
    $('#itemsContainer').append(getItemTemplate(itemIndex));
    itemIndex++;
    updateItemNumbers();
});

// ── Remove Item ──────────────────────────────────────────────────────────────
$(document).on('click', '.remove-item', function () {
    $(this).closest('.item-row').remove();
    updateItemNumbers();
    if ($('.item-row').length === 0) {
        $('#itemsContainer').prepend(`
            <div id="emptyItemsMsg" class="text-center text-muted py-4">
                <i class="ri-list-check" style="font-size: 2rem;"></i>
                <p class="mt-2 mb-0">Belum ada item evaluasi. Klik <strong>"Tambah Item"</strong> untuk memulai.</p>
            </div>
        `);
    }
});

// ── Add Peserta ───────────────────────────────────────────────────────────────
$('#addParticipant').on('click', function () {
    $('#emptyParticipantsMsg').remove();
    $('#participantsContainer').append(getParticipantTemplate(participantIndex));
    participantIndex++;
});

// ── Remove Peserta ────────────────────────────────────────────────────────────
$(document).on('click', '.remove-participant', function () {
    $(this).closest('.participant-row').remove();
    updateParticipantNumbers();
    if ($('.participant-row').length === 0) {
        $('#participantsContainer').prepend(`
            <div id="emptyParticipantsMsg" class="text-center text-muted py-3">
                <i class="ri-group-line" style="font-size: 1.5rem;"></i>
                <p class="mt-1 mb-0 small">Belum ada peserta. Klik <strong>"Tambah Peserta"</strong>.</p>
            </div>
        `);
    }
});

// ── Numbering helpers ────────────────────────────────────────────────────────
function updateItemNumbers() {
    $('.item-row').each(function (i) {
        $(this).find('.item-number').text(i + 1);
        $(this).find('input[name*="[order]"]').val(i + 1);
    });
}

function updateParticipantNumbers() {
    $('.participant-row').each(function (i) {
        $(this).find('.participant-number').text(i + 1);
    });
}
</script>
@endpush

@push('styles')
<style>
.item-row        { border-left: 4px solid #0d6efd !important; background: #f8f9ff; }
.participant-row { border-left: 4px solid #0dcaf0 !important; background: #f8feff; }
</style>
@endpush