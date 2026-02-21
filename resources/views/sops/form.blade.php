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

{{-- Informasi Dasar SOP --}}
<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0"><i class="ri-information-line me-2"></i>Informasi Dasar SOP</h6>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Nama SOP <span class="text-danger">*</span></label>
                <input type="text" name="nama_sop" class="form-control"
                       value="{{ old('nama_sop', $sop->nama_sop ?? '') }}"
                       placeholder="e.g. Prosedur Pelatihan Karyawan" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">No. SOP <span class="text-danger">*</span></label>
                <input type="text" name="no_sop" class="form-control"
                       value="{{ old('no_sop', $sop->no_sop ?? '') }}"
                       placeholder="e.g. SOP-HRD-001" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Tanggal Dibuat <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_dibuat" class="form-control"
                       value="{{ old('tanggal_dibuat', $sop->tanggal_dibuat ?? date('Y-m-d')) }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Tanggal Efektif <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_efektif" class="form-control"
                       value="{{ old('tanggal_efektif', $sop->tanggal_efektif ?? date('Y-m-d')) }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Revisi</label>
                <input type="text" name="revisi" class="form-control"
                       value="{{ old('revisi', $sop->revisi ?? '00') }}"
                       placeholder="e.g. 00, 01, 02">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Judul Header</label>
                <input type="text" name="judul_header" class="form-control"
                       value="{{ old('judul_header', $sop->judul_header ?? 'PROSEDUR TETAP (PROTAP) PELATIHAN KARYAWAN') }}"
                       placeholder="e.g. PROSEDUR TETAP (PROTAP)">
            </div>

            <div class="col-md-3">
                <label class="form-label">Logo Perusahaan</label>
                <input type="file" name="logo" class="form-control" accept="image/*">
                @if(isset($sop) && $sop->logo_path)
                    <small class="text-muted">Logo saat ini: 
                        <a href="{{ Storage::url($sop->logo_path) }}" target="_blank" class="text-primary">Lihat</a>
                    </small>
                @endif
            </div>

            <div class="col-md-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="draft" {{ old('status', $sop->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ old('status', $sop->status ?? '') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="archived" {{ old('status', $sop->status ?? '') == 'archived' ? 'selected' : '' }}>Arsip</option>
                </select>
            </div>
        </div>
    </div>
</div>

{{-- Sections Dinamis --}}
<div class="card mb-3">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="ri-list-check-2 me-2"></i>Konten SOP (Sections)</h6>
        <button type="button" class="btn btn-sm btn-light" id="addSection">
            <i class="ri-add-line me-1"></i>Tambah Section
        </button>
    </div>
    <div class="card-body">
        <div id="sectionsContainer">
            @if(isset($sop) && $sop->sections->count() > 0)
                @foreach($sop->sections as $sectionIndex => $section)
                    <div class="section-block mb-4 p-3 border rounded bg-light" data-section-index="{{ $sectionIndex }}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">
                                <span class="badge bg-primary">Section {{ $sectionIndex + 1 }}</span>
                            </h6>
                            <button type="button" class="btn btn-sm btn-danger removeSection">
                                <i class="ri-delete-bin-line"></i> Hapus
                            </button>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label class="form-label">Kode <span class="text-danger">*</span></label>
                                <input type="text" name="sections[{{ $sectionIndex }}][section_code]" 
                                       class="form-control" value="{{ $section->section_code }}" 
                                       placeholder="A" required>
                            </div>
                            <div class="col-md-10">
                                <label class="form-label">Judul Section <span class="text-danger">*</span></label>
                                <input type="text" name="sections[{{ $sectionIndex }}][section_title]" 
                                       class="form-control" value="{{ $section->section_title }}" 
                                       placeholder="e.g. Tujuan" required>
                            </div>
                        </div>

                        <div class="items-container">
                            <label class="form-label fw-bold">Item/Poin dalam Section:</label>
                            @foreach($section->items as $itemIndex => $item)
                                <div class="input-group mb-2">
                                    <span class="input-group-text">{{ $itemIndex + 1 }}.</span>
                                    <input type="text" name="sections[{{ $sectionIndex }}][items][{{ $itemIndex }}][content]" 
                                           class="form-control" value="{{ $item->content }}" 
                                           placeholder="Masukkan poin..." required>
                                    <input type="hidden" name="sections[{{ $sectionIndex }}][items][{{ $itemIndex }}][level]" value="1">
                                    <button type="button" class="btn btn-outline-danger removeItem">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 addItem">
                            <i class="ri-add-line me-1"></i>Tambah Item
                        </button>
                    </div>
                @endforeach
            @else
                {{-- Template default untuk section baru --}}
                <div class="section-block mb-4 p-3 border rounded bg-light" data-section-index="0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">
                            <span class="badge bg-primary">Section 1</span>
                        </h6>
                        <button type="button" class="btn btn-sm btn-danger removeSection">
                            <i class="ri-delete-bin-line"></i> Hapus
                        </button>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label class="form-label">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="sections[0][section_code]" class="form-control" 
                                   value="A" placeholder="A" required>
                        </div>
                        <div class="col-md-10">
                            <label class="form-label">Judul Section <span class="text-danger">*</span></label>
                            <input type="text" name="sections[0][section_title]" class="form-control" 
                                   value="Tujuan" placeholder="e.g. Tujuan" required>
                        </div>
                    </div>

                    <div class="items-container">
                        <label class="form-label fw-bold">Item/Poin dalam Section:</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text">1.</span>
                            <input type="text" name="sections[0][items][0][content]" class="form-control" 
                                   placeholder="Masukkan poin..." required>
                            <input type="hidden" name="sections[0][items][0][level]" value="1">
                            <button type="button" class="btn btn-outline-danger removeItem">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary mt-2 addItem">
                        <i class="ri-add-line me-1"></i>Tambah Item
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Approvals (Signature) --}}
<div class="card mb-3">
    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="ri-checkbox-circle-line me-2"></i>Persetujuan & Tanda Tangan</h6>
        <button type="button" class="btn btn-sm btn-light" id="addApproval">
            <i class="ri-add-line me-1"></i>Tambah Approval
        </button>
    </div>
    <div class="card-body">
        <div id="approvalsContainer">
            @if(isset($sop) && $sop->approvals->count() > 0)
                @foreach($sop->approvals as $approvalIndex => $approval)
                    <div class="approval-block mb-3 p-3 border rounded" data-approval-index="{{ $approvalIndex }}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>Approval {{ $approvalIndex + 1 }}</strong>
                            <button type="button" class="btn btn-sm btn-danger removeApproval">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                                <input type="text" name="approvals[{{ $approvalIndex }}][keterangan]" 
                                       class="form-control" value="{{ $approval->keterangan }}" 
                                       placeholder="e.g. Dibuat Oleh" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nama</label>
                                <input type="text" name="approvals[{{ $approvalIndex }}][nama]" 
                                       class="form-control" value="{{ $approval->nama }}" 
                                       placeholder="e.g. John Doe">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jabatan</label>
                                <input type="text" name="approvals[{{ $approvalIndex }}][jabatan]" 
                                       class="form-control" value="{{ $approval->jabatan }}" 
                                       placeholder="e.g. Manager HRD">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal TTD</label>
                                <input type="date" name="approvals[{{ $approvalIndex }}][tanda_tangan]" 
                                       class="form-control" value="{{ $approval->tanda_tangan }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{-- Template default approval --}}
                <div class="approval-block mb-3 p-3 border rounded" data-approval-index="0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Approval 1</strong>
                        <button type="button" class="btn btn-sm btn-danger removeApproval">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                            <input type="text" name="approvals[0][keterangan]" class="form-control" 
                                   value="Dibuat Oleh" placeholder="e.g. Dibuat Oleh" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="approvals[0][nama]" class="form-control" 
                                   placeholder="e.g. John Doe">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="approvals[0][jabatan]" class="form-control" 
                                   placeholder="e.g. Manager HRD">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal TTD</label>
                            <input type="date" name="approvals[0][tanda_tangan]" class="form-control">
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let sectionCounter = {{ isset($sop) && $sop->sections->count() > 0 ? $sop->sections->count() : 1 }};
    let approvalCounter = {{ isset($sop) && $sop->approvals->count() > 0 ? $sop->approvals->count() : 1 }};

    // Add Section
    $('#addSection').click(function() {
        const sectionHTML = `
            <div class="section-block mb-4 p-3 border rounded bg-light" data-section-index="${sectionCounter}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">
                        <span class="badge bg-primary">Section ${sectionCounter + 1}</span>
                    </h6>
                    <button type="button" class="btn btn-sm btn-danger removeSection">
                        <i class="ri-delete-bin-line"></i> Hapus
                    </button>
                </div>

                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label">Kode <span class="text-danger">*</span></label>
                        <input type="text" name="sections[${sectionCounter}][section_code]" class="form-control" placeholder="A" required>
                    </div>
                    <div class="col-md-10">
                        <label class="form-label">Judul Section <span class="text-danger">*</span></label>
                        <input type="text" name="sections[${sectionCounter}][section_title]" class="form-control" placeholder="e.g. Tujuan" required>
                    </div>
                </div>

                <div class="items-container">
                    <label class="form-label fw-bold">Item/Poin dalam Section:</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text">1.</span>
                        <input type="text" name="sections[${sectionCounter}][items][0][content]" class="form-control" placeholder="Masukkan poin..." required>
                        <input type="hidden" name="sections[${sectionCounter}][items][0][level]" value="1">
                        <button type="button" class="btn btn-outline-danger removeItem">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-outline-primary mt-2 addItem">
                    <i class="ri-add-line me-1"></i>Tambah Item
                </button>
            </div>
        `;
        $('#sectionsContainer').append(sectionHTML);
        sectionCounter++;
        updateSectionNumbers();
    });

    // Remove Section
    $(document).on('click', '.removeSection', function() {
        if ($('.section-block').length > 1) {
            $(this).closest('.section-block').remove();
            updateSectionNumbers();
        } else {
            Swal.fire('Perhatian!', 'Minimal harus ada 1 section', 'warning');
        }
    });

    // Add Item to Section
    $(document).on('click', '.addItem', function() {
        const sectionBlock = $(this).closest('.section-block');
        const sectionIndex = sectionBlock.data('section-index');
        const itemsContainer = sectionBlock.find('.items-container');
        const itemCount = itemsContainer.find('.input-group').length;

        const itemHTML = `
            <div class="input-group mb-2">
                <span class="input-group-text">${itemCount + 1}.</span>
                <input type="text" name="sections[${sectionIndex}][items][${itemCount}][content]" class="form-control" placeholder="Masukkan poin..." required>
                <input type="hidden" name="sections[${sectionIndex}][items][${itemCount}][level]" value="1">
                <button type="button" class="btn btn-outline-danger removeItem">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        `;
        itemsContainer.append(itemHTML);
        updateItemNumbers(sectionBlock);
    });

    // Remove Item
    $(document).on('click', '.removeItem', function() {
        const sectionBlock = $(this).closest('.section-block');
        const itemsContainer = sectionBlock.find('.items-container');
        
        if (itemsContainer.find('.input-group').length > 1) {
            $(this).closest('.input-group').remove();
            updateItemNumbers(sectionBlock);
        } else {
            Swal.fire('Perhatian!', 'Minimal harus ada 1 item dalam section', 'warning');
        }
    });

    // Add Approval
    $('#addApproval').click(function() {
        const approvalHTML = `
            <div class="approval-block mb-3 p-3 border rounded" data-approval-index="${approvalCounter}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Approval ${approvalCounter + 1}</strong>
                    <button type="button" class="btn btn-sm btn-danger removeApproval">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                        <input type="text" name="approvals[${approvalCounter}][keterangan]" class="form-control" placeholder="e.g. Dibuat Oleh" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="approvals[${approvalCounter}][nama]" class="form-control" placeholder="e.g. John Doe">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="approvals[${approvalCounter}][jabatan]" class="form-control" placeholder="e.g. Manager HRD">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal TTD</label>
                        <input type="date" name="approvals[${approvalCounter}][tanda_tangan]" class="form-control">
                    </div>
                </div>
            </div>
        `;
        $('#approvalsContainer').append(approvalHTML);
        approvalCounter++;
    });

    // Remove Approval
    $(document).on('click', '.removeApproval', function() {
        if ($('.approval-block').length > 1) {
            $(this).closest('.approval-block').remove();
        } else {
            Swal.fire('Perhatian!', 'Minimal harus ada 1 approval', 'warning');
        }
    });

    // Update section numbers
    function updateSectionNumbers() {
        $('.section-block').each(function(index) {
            $(this).find('.badge').first().text(`Section ${index + 1}`);
        });
    }

    // Update item numbers in a section
    function updateItemNumbers(sectionBlock) {
        sectionBlock.find('.items-container .input-group').each(function(index) {
            $(this).find('.input-group-text').text(`${index + 1}.`);
        });
    }
});
</script>
@endpush