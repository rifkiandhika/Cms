@extends('layouts.app')

@section('title', 'Isi Audit')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('audits.index') }}">Audit</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Isi Audit</li>
@endsection

@section('content')
<div class="app-body">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0"><i class="ri-file-edit-line me-2"></i>{{ $audit->title }}</h4>
                    <small>
                        <i class="ri-calendar-line me-1"></i>Tanggal: {{ $audit->audit_date->format('d/m/Y') }} |
                        <i class="ri-user-line me-1 ms-2"></i>Auditor: {{ $audit->auditor_name ?? '-' }}
                    </small>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    @if($audit->status != 'completed')
                        <button type="button" class="btn btn-success btn-sm" onclick="completeAudit()">
                            <i class="ri-check-line me-1"></i>Selesaikan Audit
                        </button>
                    @else
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="ri-checkbox-circle-line me-1"></i>Selesai
                        </span>
                    @endif
                    <a href="{{ route('audits.index') }}" class="btn btn-light btn-sm">
                        <i class="ri-arrow-left-line me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Category Tabs -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white pb-0">
            <div class="tab-scroll-wrapper" style="overflow-x:auto; white-space:nowrap;">
                <ul class="nav nav-tabs flex-nowrap" id="categoryTabs" role="tablist" style="border-bottom:none;">
                    @foreach($categories as $index => $category)
                        <li class="nav-item" role="presentation" style="display:inline-block;">
                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                    id="tab-cat-{{ $category->id }}"
                                    data-bs-toggle="tab"
                                    data-bs-target="#content-cat-{{ $category->id }}"
                                    type="button" role="tab"
                                    title="{{ $category->name }}">
                                <span class="tab-number">{{ $category->number }}</span>
                                <span class="tab-label d-none d-lg-inline ms-1">{{ Str::limit($category->name, 18) }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="categoryTabContent">
                @foreach($categories as $index => $category)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                         id="content-cat-{{ $category->id }}"
                         role="tabpanel">

                        <!-- Category Header -->
                        <div class="px-4 py-3 border-bottom bg-info bg-opacity-10">
                            <h5 class="mb-0 text-info fw-bold">
                                <i class="ri-folder-open-line me-2"></i>
                                {{ $category->number }}. {{ $category->name }}
                            </h5>
                        </div>

                        <div class="p-4">
                            @foreach($category->subCategories as $subCategory)
                                <div class="mb-4">
                                    <!-- Sub Category Label -->
                                    @if($subCategory->label)
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded px-3 py-2 me-2 fw-bold">
                                                {{ $subCategory->label }}
                                            </div>
                                            <h6 class="mb-0 text-dark">{{ $subCategory->name }}</h6>
                                        </div>
                                    @else
                                        <h6 class="mb-3 text-dark border-start border-primary border-3 ps-3">
                                            {{ $subCategory->name }}
                                        </h6>
                                    @endif

                                    @foreach($subCategory->questions as $question)
                                        @php
                                            $response = $question->auditResponses->first();
                                            $sopLink  = \App\Helpers\AuditHelper::getSopLink($question->number);
                                        @endphp

                                        <div class="question-item mb-3 p-4 border rounded shadow-sm
                                            {{ $response ? 'border-success bg-success bg-opacity-10' : 'bg-light' }}"
                                             data-question-id="{{ $question->id }}">

                                            <div class="d-flex justify-content-between align-items-start gap-3">
                                                <!-- Pertanyaan -->
                                                <div class="flex-grow-1">
                                                    <label class="form-label fw-bold text-primary mb-2">
                                                        <i class="ri-question-line me-1"></i>
                                                        {{ $question->number }}. {{ $question->question }}
                                                    </label>

                                                    <!-- Status Badge -->
                                                    @if($response)
                                                        <div class="mt-2 d-flex flex-wrap gap-2 align-items-center">
                                                            {{-- <span class="badge bg-success">
                                                                <i class="ri-check-line me-1"></i>Sudah Dijawab
                                                            </span>
                                                            @if($response->response)
                                                                <span class="badge
                                                                    {{ $response->response == 'yes' ? 'bg-success' :
                                                                       ($response->response == 'no' ? 'bg-danger' :
                                                                       ($response->response == 'partial' ? 'bg-warning text-dark' : 'bg-secondary')) }}">
                                                                    {{ match($response->response) {
                                                                        'yes' => 'Ya',
                                                                        'no' => 'Tidak',
                                                                        'partial' => 'Sebagian',
                                                                        'na' => 'N/A',
                                                                        default => $response->response
                                                                    } }}
                                                                </span>
                                                            @endif --}}
                                                            @if($response->evidence)
                                                                <span class="text-muted small">
                                                                    <i class="ri-file-text-line me-1"></i>Ada catatan bukti
                                                                </span>
                                                            @endif
                                                            @if($response->document_path)
                                                                <a href="{{ Storage::url($response->document_path) }}"
                                                                   target="_blank"
                                                                   class="badge bg-info text-decoration-none">
                                                                    <i class="ri-file-text-line me-1"></i>Dokumen
                                                                </a>
                                                            @endif
                                                            @if($response->image_path)
                                                                <a href="{{ Storage::url($response->image_path) }}"
                                                                   target="_blank"
                                                                   class="badge bg-purple text-decoration-none">
                                                                    <i class="ri-image-line me-1"></i>Gambar
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="mt-2">
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="ri-time-line me-1"></i>Belum Dijawab
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="d-flex flex-column gap-2 text-nowrap">
                                                    {{-- @if($audit->status != 'completed')
                                                        <button type="button"
                                                                class="btn btn-sm btn-primary btn-isi-jawaban"
                                                                data-question-id="{{ $question->id }}"
                                                                data-question-number="{{ $question->number }}"
                                                                data-question-text="{{ $question->question }}"
                                                                data-response="{{ $response?->response }}"
                                                                data-evidence="{{ $response?->evidence }}"
                                                                data-notes="{{ $response?->notes }}"
                                                                data-evidence-date="{{ $response?->evidence_date?->format('Y-m-d') }}"
                                                                data-temperature="{{ $response?->temperature }}"
                                                                data-audit-id="{{ $audit->id }}">
                                                            <i class="ri-edit-line me-1"></i>
                                                            {{ $response ? 'Edit Jawaban' : 'Isi Jawaban' }}
                                                        </button>
                                                    @endif --}}

                                                    @if($sopLink)
                                                        <a href="{{ $sopLink['url'] }}"
                                                           class="btn btn-sm btn-outline-info"
                                                           @if($sopLink['new_tab']) target="_blank" @endif>
                                                            <i class="ri-{{ $sopLink['icon'] }} me-1"></i>
                                                            {{ $sopLink['label'] }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        <!-- Navigasi antar kategori -->
                        <div class="px-4 pb-4 d-flex justify-content-between">
                            @if($index > 0)
                                <button class="btn btn-outline-secondary btn-sm" onclick="goToTab({{ $index - 1 }})">
                                    <i class="ri-arrow-left-line me-1"></i>Kategori Sebelumnya
                                </button>
                            @else
                                <div></div>
                            @endif
                            @if($index < $categories->count() - 1)
                                <button class="btn btn-primary btn-sm" onclick="goToTab({{ $index + 1 }})">
                                    Kategori Berikutnya <i class="ri-arrow-right-line ms-1"></i>
                                </button>
                            @else
                                @if($audit->status != 'completed')
                                    <button type="button" class="btn btn-success btn-sm" onclick="completeAudit()">
                                        <i class="ri-check-double-line me-1"></i>Selesaikan Audit
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ====== MODAL ISI JAWABAN ====== --}}
<div class="modal fade" id="modalIsiJawaban" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="ri-edit-line me-2"></i>Isi Jawaban
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modal-question-info" class="alert alert-light border mb-3">
                    <strong id="modal-question-number" class="text-primary"></strong>
                    <p id="modal-question-text" class="mb-0 mt-1"></p>
                </div>

                <div class="row g-3">
                    <!-- Jawaban -->
                    <div class="col-12">
                        <label class="form-label fw-bold">Jawaban <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="modal_response" id="modal_yes" value="yes">
                            <label class="btn btn-outline-success" for="modal_yes">
                                <i class="ri-check-line me-1"></i>Ya
                            </label>

                            <input type="radio" class="btn-check" name="modal_response" id="modal_no" value="no">
                            <label class="btn btn-outline-danger" for="modal_no">
                                <i class="ri-close-line me-1"></i>Tidak
                            </label>

                            <input type="radio" class="btn-check" name="modal_response" id="modal_partial" value="partial">
                            <label class="btn btn-outline-warning" for="modal_partial">
                                <i class="ri-subtract-line me-1"></i>Sebagian
                            </label>

                            <input type="radio" class="btn-check" name="modal_response" id="modal_na" value="na">
                            <label class="btn btn-outline-secondary" for="modal_na">
                                <i class="ri-indeterminate-circle-line me-1"></i>N/A
                            </label>
                        </div>
                    </div>

                    <!-- Bukti/Catatan -->
                    <div class="col-md-6">
                        <label class="form-label">Bukti / Catatan</label>
                        <textarea id="modal_evidence" class="form-control" rows="3"
                                  placeholder="Masukkan bukti atau catatan..."></textarea>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div class="col-md-6">
                        <label class="form-label">Catatan Tambahan</label>
                        <textarea id="modal_notes" class="form-control" rows="3"
                                  placeholder="Catatan tambahan..."></textarea>
                    </div>

                    <!-- Tanggal Bukti -->
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Bukti</label>
                        <input type="date" id="modal_evidence_date" class="form-control">
                    </div>

                    <!-- Suhu -->
                    <div class="col-md-6">
                        <label class="form-label">Suhu (°C)</label>
                        <input type="number" id="modal_temperature" class="form-control"
                               step="0.01" placeholder="Contoh: 25.50">
                    </div>

                    <!-- Upload Dokumen -->
                    <div class="col-md-6">
                        <label class="form-label">Upload Dokumen</label>
                        <input type="file" id="modal_document" class="form-control"
                               accept=".pdf,.doc,.docx,.xls,.xlsx">
                        <small class="text-muted">PDF, DOC, DOCX, XLS, XLSX (Max: 10MB)</small>
                    </div>

                    <!-- Upload Gambar -->
                    <div class="col-md-6">
                        <label class="form-label">Upload Gambar</label>
                        <input type="file" id="modal_image" class="form-control" accept="image/*">
                        <small class="text-muted">JPG, PNG, GIF (Max: 5MB)</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanJawaban">
                    <i class="ri-save-line me-1"></i>Simpan Jawaban
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    #categoryTabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 0.75rem 1rem;
        transition: all 0.2s;
    }
    #categoryTabs .nav-link:hover {
        color: #0d6efd;
        background: rgba(13,110,253,0.05);
    }
    #categoryTabs .nav-link.active {
        color: #0d6efd;
        background: transparent;
        border-color: transparent transparent #0d6efd;
    }

    .question-item {
        transition: all 0.25s ease;
        border-radius: 0.5rem;
    }
    .question-item:hover {
        box-shadow: 0 4px 14px rgba(0,0,0,0.12) !important;
        transform: translateY(-1px);
    }

    .bg-purple { background-color: #6f42c1 !important; }

    .tab-scroll-wrapper::-webkit-scrollbar { height: 4px; }
    .tab-scroll-wrapper::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 4px; }

    @media (max-width: 768px) {
        .btn-group { flex-direction: column; }
        .btn-group > .btn { width: 100%; border-radius: 0.25rem !important; margin-bottom: 4px; }
        #categoryTabs .nav-link { padding: 0.5rem 0.6rem; font-size: 0.85rem; }
    }
</style>
@endpush

@push('scripts')
<script>
// ==========================================
// TAB NAVIGATION
// ==========================================
const tabButtons = document.querySelectorAll('#categoryTabs .nav-link');

function goToTab(index) {
    if (tabButtons[index]) {
        tabButtons[index].click();
        tabButtons[index].scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const activeTab = document.querySelector('#categoryTabs .nav-link.active');
    if (activeTab) activeTab.scrollIntoView({ inline: 'center', block: 'nearest' });
});

// ==========================================
// MODAL ISI JAWABAN
// ==========================================
let currentQuestionId = null;
let currentAuditId    = null;

document.querySelectorAll('.btn-isi-jawaban').forEach(btn => {
    btn.addEventListener('click', function() {
        currentQuestionId = this.dataset.questionId;
        currentAuditId    = this.dataset.auditId;

        document.getElementById('modal-question-number').textContent = this.dataset.questionNumber + '.';
        document.getElementById('modal-question-text').textContent   = this.dataset.questionText;

        // Reset form
        document.querySelectorAll('input[name="modal_response"]').forEach(r => r.checked = false);
        document.getElementById('modal_evidence').value      = '';
        document.getElementById('modal_notes').value         = '';
        document.getElementById('modal_evidence_date').value = '';
        document.getElementById('modal_temperature').value   = '';
        document.getElementById('modal_document').value      = '';
        document.getElementById('modal_image').value         = '';

        // Isi nilai yang sudah ada
        const savedResponse = this.dataset.response;
        if (savedResponse) {
            const radioEl = document.getElementById('modal_' + savedResponse);
            if (radioEl) radioEl.checked = true;
        }
        if (this.dataset.evidence)     document.getElementById('modal_evidence').value      = this.dataset.evidence;
        if (this.dataset.notes)        document.getElementById('modal_notes').value         = this.dataset.notes;
        if (this.dataset.evidenceDate) document.getElementById('modal_evidence_date').value = this.dataset.evidenceDate;
        if (this.dataset.temperature)  document.getElementById('modal_temperature').value   = this.dataset.temperature;

        new bootstrap.Modal(document.getElementById('modalIsiJawaban')).show();
    });
});

// ==========================================
// SIMPAN JAWABAN VIA AJAX
// ==========================================
document.getElementById('btnSimpanJawaban').addEventListener('click', function() {
    const btn = this;
    const formData = new FormData();

    const response = document.querySelector('input[name="modal_response"]:checked');
    if (response) formData.append('response', response.value);

    formData.append('evidence',      document.getElementById('modal_evidence').value);
    formData.append('notes',         document.getElementById('modal_notes').value);
    formData.append('evidence_date', document.getElementById('modal_evidence_date').value);
    formData.append('temperature',   document.getElementById('modal_temperature').value);

    const docInput = document.getElementById('modal_document');
    if (docInput.files.length > 0) formData.append('document', docInput.files[0]);

    const imgInput = document.getElementById('modal_image');
    if (imgInput.files.length > 0) formData.append('image', imgInput.files[0]);

    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';
    btn.disabled  = true;

    fetch(`/audits/${currentAuditId}/questions/${currentQuestionId}/response`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalIsiJawaban')).hide();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Jawaban berhasil disimpan.',
                timer: 1800,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan.' });
        }
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat menyimpan data.' });
    })
    .finally(() => {
        btn.innerHTML = '<i class="ri-save-line me-1"></i>Simpan Jawaban';
        btn.disabled  = false;
    });
});

// ==========================================
// SELESAIKAN AUDIT
// ==========================================
function completeAudit() {
    Swal.fire({
        title: 'Selesaikan Audit?',
        html: `Status audit akan berubah menjadi <strong>Selesai</strong>.<br>
               Pastikan semua pertanyaan sudah dijawab.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ri-check-double-line me-1"></i>Ya, Selesaikan!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("audits.complete", $audit) }}';
            form.innerHTML = `@csrf`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush