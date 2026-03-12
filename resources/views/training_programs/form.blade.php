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
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="ri-information-line me-2"></i>Informasi Program</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="hidden" name="sop_id" value="{{ old('sop_id', request('sop_id') ?? $trainingProgram->sop_id ?? '') }}">
                <label class="form-label">Judul Program <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" 
                       value="{{ old('title', $trainingProgram->title ?? '') }}"
                       placeholder="e.g. PROSEDUR TETAP (PROTAP) PELATIHAN KARYAWAN PT. PREMIERE ALKES NUSINDO"
                       required>
                <small class="text-muted">Nama lengkap program pelatihan</small>
            </div>

            <div class="col-md-3">
                <label class="form-label">No. Program <span class="text-danger">*</span></label>
                <input type="text" name="program_number" class="form-control" 
                       value="{{ old('program_number', $trainingProgram->program_number ?? '') }}"
                       placeholder="e.g. 01.PROTAP.CDAKB"
                       required>
                <small class="text-muted">Nomor unik program</small>
            </div>

            <div class="col-md-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="draft" {{ old('status', $trainingProgram->status ?? 'draft') == 'draft' ? 'selected' : '' }}>
                        Draft
                    </option>
                    <option value="active" {{ old('status', $trainingProgram->status ?? '') == 'active' ? 'selected' : '' }}>
                        Aktif
                    </option>
                    <option value="archived" {{ old('status', $trainingProgram->status ?? '') == 'archived' ? 'selected' : '' }}>
                        Arsip
                    </option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Tanggal Efektif</label>
                <input type="date" name="effective_date" class="form-control" 
                       value="{{ old('effective_date', isset($trainingProgram->effective_date) ? $trainingProgram->effective_date->format('Y-m-d') : '') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Revisi</label>
                <input type="text" name="revision" class="form-control" 
                       value="{{ old('revision', $trainingProgram->revision ?? 'Rev. 00') }}"
                       placeholder="Rev. 00">
                <small class="text-muted">e.g. Rev. 00, Rev. 01</small>
            </div>

            <div class="col-md-6">
                <label class="form-label">Deskripsi/Keterangan</label>
                <textarea name="description" class="form-control" rows="2" 
                          placeholder="Deskripsi singkat program pelatihan (opsional)">{{ old('description', $trainingProgram->description ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- Main Categories Section --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="ri-folder-line me-2"></i>
            PROGRAM PELATIHAN KARYAWAN
        </h5>
        <button type="button" class="btn btn-light btn-sm" id="addMainCategory">
            <i class="ri-add-circle-line me-1"></i>Tambah Main Category
        </button>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="ri-information-line me-2"></i>
            <strong>Panduan Pengisian:</strong>
            <ul class="mb-0 mt-2 small">
                <li><strong>Main Category (Roman):</strong> I, II, III - Level tertinggi (e.g., PELATIHAN UMUM)</li>
                <li><strong>Sub Category (Letter):</strong> A, B, C - Di bawah Main Category (e.g., ORIENTASI UMUM)</li>
                <li><strong>Training Item (Number):</strong> 1, 2, 3 - Program pelatihan spesifik (e.g., Pengenalan Perusahaan)</li>
                <li><strong>Detail (Letter):</strong> a, b, c - Detail dari Training Item (e.g., Sejarah Perusahaan)</li>
            </ul>
        </div>

        <div id="mainCategoriesContainer">
            @if(isset($trainingProgram) && $trainingProgram->mainCategories->count() > 0)
                @foreach($trainingProgram->mainCategories as $mcIndex => $mainCategory)
                    <div class="main-category-item border rounded p-4 mb-4" 
                         style="background: linear-gradient(to right, #f8f9fa 0%, #ffffff 100%);" 
                         data-index="{{ $mcIndex }}">
                        <input type="hidden" name="main_categories[{{ $mcIndex }}][id]" value="{{ $mainCategory->id }}">
                        
                        {{-- Main Category Header --}}
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <h5 class="mb-0 text-primary">
                                <i class="ri-bookmark-line me-2"></i>
                                Main Category #<span class="main-cat-number">{{ $mcIndex + 1 }}</span>
                            </h5>
                            <button type="button" class="btn btn-danger btn-sm remove-main-category">
                                <i class="ri-delete-bin-line me-1"></i>Hapus Main Category
                            </button>
                        </div>

                        {{-- Main Category Fields --}}
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <label class="fw-bold">Roman Number <span class="text-danger">*</span></label>
                                <input type="text" name="main_categories[{{ $mcIndex }}][roman_number]" 
                                       class="form-control" 
                                       value="{{ old('main_categories.'.$mcIndex.'.roman_number', $mainCategory->roman_number) }}"
                                       placeholder="I" required>
                                <small class="text-muted">I, II, III, IV</small>
                            </div>

                            <div class="col-md-8">
                                <label class="fw-bold">Nama Main Category <span class="text-danger">*</span></label>
                                <input type="text" name="main_categories[{{ $mcIndex }}][name]" 
                                       class="form-control"
                                       value="{{ old('main_categories.'.$mcIndex.'.name', $mainCategory->name) }}"
                                       placeholder="e.g. PELATIHAN UMUM" required>
                            </div>

                            <div class="col-md-2">
                                <label class="fw-bold">Urutan <span class="text-danger">*</span></label>
                                <input type="number" name="main_categories[{{ $mcIndex }}][order]" 
                                       class="form-control"
                                       value="{{ old('main_categories.'.$mcIndex.'.order', $mainCategory->order) }}"
                                       min="0" required>
                            </div>
                        </div>

                        {{-- Sub Categories Section --}}
                        <div class="sub-categories-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-success">
                                    <i class="ri-folder-2-line me-2"></i>Sub Categories (A, B, C)
                                </h6>
                                <button type="button" class="btn btn-success btn-sm add-sub-category" 
                                        data-main-index="{{ $mcIndex }}">
                                    <i class="ri-add-line me-1"></i>Tambah Sub Category
                                </button>
                            </div>

                            <div class="sub-categories-container">
                                @if($mainCategory->subCategories->count() > 0)
                                    @foreach($mainCategory->subCategories as $scIndex => $subCategory)
                                        @include('training_programs._sub_category', [
                                            'mcIndex' => $mcIndex,
                                            'scIndex' => $scIndex,
                                            'subCategory' => $subCategory
                                        ])
                                    @endforeach
                                @else
                                    <p class="text-muted small mb-0">Belum ada sub category. Klik "Tambah Sub Category".</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted text-center mb-0">Belum ada main category. Klik "Tambah Main Category" untuk memulai.</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
let mainCategoryIndex = {{ isset($trainingProgram) ? $trainingProgram->mainCategories->count() : 0 }};

// Template Main Category
function getMainCategoryTemplate(index) {
    return `
        <div class="main-category-item border rounded p-4 mb-4" 
             style="background: linear-gradient(to right, #f8f9fa 0%, #ffffff 100%);" 
             data-index="${index}">
            
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="ri-bookmark-line me-2"></i>
                    Main Category #<span class="main-cat-number">${index + 1}</span>
                </h5>
                <button type="button" class="btn btn-danger btn-sm remove-main-category">
                    <i class="ri-delete-bin-line me-1"></i>Hapus Main Category
                </button>
            </div>

            <div class="row mb-4">
                <div class="col-md-2">
                    <label class="fw-bold">Roman Number <span class="text-danger">*</span></label>
                    <input type="text" name="main_categories[${index}][roman_number]" 
                           class="form-control" 
                           placeholder="I" required>
                    <small class="text-muted">I, II, III, IV</small>
                </div>

                <div class="col-md-8">
                    <label class="fw-bold">Nama Main Category <span class="text-danger">*</span></label>
                    <input type="text" name="main_categories[${index}][name]" 
                           class="form-control"
                           placeholder="e.g. PELATIHAN UMUM" required>
                </div>

                <div class="col-md-2">
                    <label class="fw-bold">Urutan <span class="text-danger">*</span></label>
                    <input type="number" name="main_categories[${index}][order]" 
                           class="form-control"
                           value="${index + 1}"
                           min="0" required>
                </div>
            </div>

            <div class="sub-categories-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-success">
                        <i class="ri-folder-2-line me-2"></i>Sub Categories (A, B, C)
                    </h6>
                    <button type="button" class="btn btn-success btn-sm add-sub-category" 
                            data-main-index="${index}">
                        <i class="ri-add-line me-1"></i>Tambah Sub Category
                    </button>
                </div>

                <div class="sub-categories-container">
                    <p class="text-muted small mb-0">Belum ada sub category. Klik "Tambah Sub Category".</p>
                </div>
            </div>
        </div>
    `;
}

// Template Sub Category
function getSubCategoryTemplate(mcIndex, scIndex) {
    return `
        <div class="sub-category-item bg-white border rounded p-3 mb-3" data-index="${scIndex}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-success">
                    <i class="ri-folder-2-line me-2"></i>
                    Sub Category #<span class="sub-cat-number">${scIndex + 1}</span>
                </h6>
                <button type="button" class="btn btn-danger btn-sm remove-sub-category">
                    <i class="ri-delete-bin-line me-1"></i>Hapus
                </button>
            </div>

            <div class="row mb-3">
                <div class="col-md-2">
                    <label class="small fw-bold">Letter <span class="text-danger">*</span></label>
                    <input type="text" name="main_categories[${mcIndex}][sub_categories][${scIndex}][letter]" 
                           class="form-control form-control-sm" 
                           placeholder="A" required>
                </div>

                <div class="col-md-8">
                    <label class="small fw-bold">Nama Sub Category <span class="text-danger">*</span></label>
                    <input type="text" name="main_categories[${mcIndex}][sub_categories][${scIndex}][name]" 
                           class="form-control form-control-sm"
                           placeholder="e.g. ORIENTASI UMUM" required>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold">Urutan <span class="text-danger">*</span></label>
                    <input type="number" name="main_categories[${mcIndex}][sub_categories][${scIndex}][order]" 
                           class="form-control form-control-sm"
                           value="${scIndex + 1}"
                           min="0" required>
                </div>
            </div>

            <div class="training-items-section border-top pt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="small fw-bold text-info">
                        <i class="ri-list-check me-2"></i>Training Items (1, 2, 3)
                    </label>
                    <button type="button" class="btn btn-info btn-sm add-training-item" 
                            data-main-index="${mcIndex}" 
                            data-sub-index="${scIndex}">
                        <i class="ri-add-line me-1"></i>Tambah Item
                    </button>
                </div>

                <div class="training-items-container">
                    <p class="text-muted small mb-0">Belum ada training item. Klik "Tambah Item".</p>
                </div>
            </div>
        </div>
    `;
}

// Template Training Item (dipendekkan, isi lengkap sama seperti sebelumnya)
function getTrainingItemTemplate(mcIndex, scIndex, tiIndex) {
    return `
        <div class="training-item bg-light border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="small text-info">
                    <i class="ri-file-text-line me-1"></i>Item #${tiIndex + 1}
                </strong>
                <button type="button" class="btn btn-danger btn-sm remove-training-item">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>

            <div class="row mb-2">
                <div class="col-md-1">
                    <label class="small">No</label>
                    <input type="text" name="main_categories[${mcIndex}][sub_categories][${scIndex}][training_items][${tiIndex}][number]" 
                           class="form-control form-control-sm" value="${tiIndex + 1}" required>
                </div>
                <div class="col-md-9">
                    <label class="small">Nama Pelatihan</label>
                    <input type="text" name="main_categories[${mcIndex}][sub_categories][${scIndex}][training_items][${tiIndex}][nama_pelatihan]" 
                           class="form-control form-control-sm" placeholder="Nama pelatihan" required>
                </div>
                <div class="col-md-2">
                    <label class="small">Urutan</label>
                    <input type="number" name="main_categories[${mcIndex}][sub_categories][${scIndex}][training_items][${tiIndex}][order]" 
                           class="form-control form-control-sm" value="${tiIndex + 1}" required>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3">
                    <label class="small">Peserta</label>
                    <textarea name="main_categories[${mcIndex}][sub_categories][${scIndex}][training_items][${tiIndex}][peserta]" 
                              class="form-control form-control-sm" rows="2"></textarea>
                </div>
                <div class="col-md-3">
                    <label class="small">Instruktur</label>
                    <input type="text" name="main_categories[${mcIndex}][sub_categories][${scIndex}][training_items][${tiIndex}][instruktur]" 
                           class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="small">Metode</label>
                    <input type="text" name="main_categories[${mcIndex}][sub_categories][${scIndex}][training_items][${tiIndex}][metode]" 
                           class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="small">Jadwal</label>
                    <input type="text" name="main_categories[${mcIndex}][sub_categories][${scIndex}][training_items][${tiIndex}][jadwal]" 
                           class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="small">Metode Penilaian</label>
                    <input type="text" name="main_categories[${mcIndex}][sub_categories][${scIndex}][training_items][${tiIndex}][metode_penilaian]" 
                           class="form-control form-control-sm">
                </div>
            </div>

            <div class="border-top pt-2 mt-2">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="fw-bold text-secondary">Details (a, b, c)</small>
                    <button type="button" class="btn btn-secondary btn-sm add-detail" 
                            data-main-index="${mcIndex}" 
                            data-sub-index="${scIndex}" 
                            data-item-index="${tiIndex}">
                        <i class="ri-add-line"></i> Detail
                    </button>
                </div>
                <div class="details-container mt-2">
                    <p class="text-muted small mb-0">Belum ada detail.</p>
                </div>
            </div>
        </div>
    `;
}

// Template Detail
function getDetailTemplate(mcIndex, scIndex, tiIndex, dIndex) {
    return `
        <div class="detail-item bg-white p-2 rounded mb-1">
            <div class="row">
                <div class="col-md-1">
                    <input type="text" name="main_categories[${mcIndex}][sub_categories][${scIndex}][training_items][${tiIndex}][details][${dIndex}][letter]" 
                           class="form-control form-control-sm" placeholder="a" required>
                </div>
                <div class="col-md-9">
                    <input type="text" name="main_categories[${mcIndex}][sub_categories][${scIndex}][training_items][${tiIndex}][details][${dIndex}][content]" 
                           class="form-control form-control-sm" placeholder="Content" required>
                </div>
                <div class="col-md-1">
                    <input type="number" name="main_categories[${mcIndex}][sub_categories][${scIndex}][training_items][${tiIndex}][details][${dIndex}][order]" 
                           class="form-control form-control-sm" value="${dIndex + 1}" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-detail w-100">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Event Handlers
$('#addMainCategory').on('click', function() {
    const container = $('#mainCategoriesContainer');
    container.find('p.text-muted').remove();
    container.append(getMainCategoryTemplate(mainCategoryIndex));
    mainCategoryIndex++;
});

$(document).on('click', '.remove-main-category', function() {
    if (confirm('Yakin hapus main category ini?')) {
        $(this).closest('.main-category-item').remove();
        if ($('.main-category-item').length === 0) {
            $('#mainCategoriesContainer').html('<p class="text-muted text-center mb-0">Belum ada main category. Klik "Tambah Main Category" untuk memulai.</p>');
        }
    }
});

$(document).on('click', '.add-sub-category', function() {
    const mcIndex = $(this).data('main-index');
    const container = $(this).closest('.sub-categories-section').find('.sub-categories-container');
    container.find('p.text-muted').remove();
    
    const scIndex = container.find('.sub-category-item').length;
    container.append(getSubCategoryTemplate(mcIndex, scIndex));
});

$(document).on('click', '.remove-sub-category', function() {
    if (confirm('Yakin hapus sub category ini?')) {
        const container = $(this).closest('.sub-categories-container');
        $(this).closest('.sub-category-item').remove();
        
        if (container.find('.sub-category-item').length === 0) {
            container.html('<p class="text-muted small mb-0">Belum ada sub category. Klik "Tambah Sub Category".</p>');
        }
    }
});

$(document).on('click', '.add-training-item', function() {
    const mcIndex = $(this).data('main-index');
    const scIndex = $(this).data('sub-index');
    const container = $(this).closest('.training-items-section').find('.training-items-container');
    container.find('p.text-muted').remove();
    
    const tiIndex = container.find('.training-item').length;
    container.append(getTrainingItemTemplate(mcIndex, scIndex, tiIndex));
});

$(document).on('click', '.remove-training-item', function() {
    if (confirm('Yakin hapus training item ini?')) {
        const container = $(this).closest('.training-items-container');
        $(this).closest('.training-item').remove();
        
        if (container.find('.training-item').length === 0) {
            container.html('<p class="text-muted small mb-0">Belum ada training item. Klik "Tambah Item".</p>');
        }
    }
});

$(document).on('click', '.add-detail', function() {
    const mcIndex = $(this).data('main-index');
    const scIndex = $(this).data('sub-index');
    const tiIndex = $(this).data('item-index');
    const container = $(this).closest('.training-item').find('.details-container');
    container.find('p.text-muted').remove();
    
    const dIndex = container.find('.detail-item').length;
    container.append(getDetailTemplate(mcIndex, scIndex, tiIndex, dIndex));
});

$(document).on('click', '.remove-detail', function() {
    const container = $(this).closest('.details-container');
    $(this).closest('.detail-item').remove();
    
    if (container.find('.detail-item').length === 0) {
        container.html('<p class="text-muted small mb-0">Belum ada detail.</p>');
    }
});
</script>
@endpush

@push('styles')
<style>
.main-category-item { border-left: 5px solid #0d6efd !important; }
.sub-category-item { border-left: 3px solid #198754 !important; }
.training-item { border-left: 3px solid #0dcaf0 !important; }
.detail-item { border-left: 2px solid #6c757d !important; }
</style>
@endpush