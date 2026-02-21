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

<!-- Category Information -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="ri-file-list-3-line me-2"></i>Informasi Kategori</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Nomor Kategori <span class="text-danger">*</span></label>
                <input type="text" name="number" class="form-control"
                       value="{{ old('number', $category->number ?? '') }}"
                       placeholder="e.g. 1, 2, 3" required>
                <small class="text-muted">Contoh: 1, 2, 3</small>
            </div>

            <div class="col-md-6">
                <label>Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name', $category->name ?? '') }}"
                       placeholder="e.g. Sistem Manajemen Mutu" required>
                <small class="text-muted">Contoh: Sistem Manajemen Mutu</small>
            </div>

            <div class="col-md-2">
                <label>Urutan <span class="text-danger">*</span></label>
                <input type="number" name="order" class="form-control"
                       value="{{ old('order', $category->order ?? 1) }}"
                       min="0" required>
            </div>
        </div>
    </div>
</div>

<!-- SubCategories Section -->
<div class="card">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="ri-folder-line me-2"></i>Sub Kategori & Pertanyaan</h5>
        <button type="button" class="btn btn-light btn-sm" id="addSubCategory">
            <i class="ri-add-circle-line me-1"></i>Tambah Sub Kategori
        </button>
    </div>
    <div class="card-body">
        <div id="subCategoriesContainer">
            @if(isset($category) && $category->subCategories->count() > 0)
                @foreach($category->subCategories as $index => $subCategory)
                    <div class="sub-category-item border rounded p-3 mb-3" data-index="{{ $index }}">
                        <input type="hidden" name="sub_categories[{{ $index }}][id]" value="{{ $subCategory->id }}">
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="ri-folder-2-line me-2"></i>Sub Kategori #<span class="sub-cat-number">{{ $index + 1 }}</span></h6>
                            <button type="button" class="btn btn-danger btn-sm remove-sub-category">
                                <i class="ri-delete-bin-line me-1"></i>Hapus
                            </button>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label>Label <span class="text-danger">*</span></label>
                                <input type="text" name="sub_categories[{{ $index }}][label]" 
                                       class="form-control" 
                                       value="{{ old('sub_categories.'.$index.'.label', $subCategory->label) }}"
                                       placeholder="e.g. a, b, c" required>
                                <small class="text-muted">Contoh: a, b, c</small>
                            </div>

                            <div class="col-md-8">
                                <label>Nama Sub Kategori <span class="text-danger">*</span></label>
                                <input type="text" name="sub_categories[{{ $index }}][name]" 
                                       class="form-control"
                                       value="{{ old('sub_categories.'.$index.'.name', $subCategory->name) }}"
                                       placeholder="e.g. Persyaratan Umum" required>
                                <small class="text-muted">Contoh: Persyaratan Umum</small>
                            </div>

                            <div class="col-md-2">
                                <label>Urutan <span class="text-danger">*</span></label>
                                <input type="number" name="sub_categories[{{ $index }}][order]" 
                                       class="form-control"
                                       value="{{ old('sub_categories.'.$index.'.order', $subCategory->order) }}"
                                       min="0" required>
                            </div>
                        </div>

                        <!-- Questions Section -->
                        <div class="questions-section border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="fw-bold"><i class="ri-question-line me-2"></i>Pertanyaan</label>
                                <button type="button" class="btn btn-info btn-sm add-question" data-subcat-index="{{ $index }}">
                                    <i class="ri-add-line me-1"></i>Tambah Pertanyaan
                                </button>
                            </div>

                            <div class="questions-container">
                                @if($subCategory->questions->count() > 0)
                                    @foreach($subCategory->questions as $qIndex => $question)
                                        <div class="question-item bg-light p-2 rounded mb-2">
                                            <input type="hidden" name="sub_categories[{{ $index }}][questions][{{ $qIndex }}][id]" value="{{ $question->id }}">
                                            
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <label class="small">Nomor</label>
                                                    <input type="text" name="sub_categories[{{ $index }}][questions][{{ $qIndex }}][number]" 
                                                           class="form-control form-control-sm"
                                                           value="{{ old('sub_categories.'.$index.'.questions.'.$qIndex.'.number', $question->number) }}"
                                                           placeholder="1.1" required>
                                                </div>

                                                <div class="col-md-7">
                                                    <label class="small">Pertanyaan</label>
                                                    <textarea name="sub_categories[{{ $index }}][questions][{{ $qIndex }}][question]" 
                                                              class="form-control form-control-sm" 
                                                              rows="2" required>{{ old('sub_categories.'.$index.'.questions.'.$qIndex.'.question', $question->question) }}</textarea>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="small">Urutan</label>
                                                    <input type="number" name="sub_categories[{{ $index }}][questions][{{ $qIndex }}][order]" 
                                                           class="form-control form-control-sm"
                                                           value="{{ old('sub_categories.'.$index.'.questions.'.$qIndex.'.order', $question->order) }}"
                                                           min="0" required>
                                                </div>

                                                <div class="col-md-1 d-flex align-items-end">
                                                    <button type="button" class="btn btn-danger btn-sm remove-question w-100">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted small mb-0">Belum ada pertanyaan. Klik "Tambah Pertanyaan" untuk menambahkan.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted text-center mb-0">Belum ada sub kategori. Klik "Tambah Sub Kategori" untuk memulai.</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
let subCategoryIndex = {{ isset($category) ? $category->subCategories->count() : 0 }};

// Template untuk Sub Category baru
function getSubCategoryTemplate(index) {
    return `
        <div class="sub-category-item border rounded p-3 mb-3" data-index="${index}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="ri-folder-2-line me-2"></i>Sub Kategori #<span class="sub-cat-number">${index + 1}</span></h6>
                <button type="button" class="btn btn-danger btn-sm remove-sub-category">
                    <i class="ri-delete-bin-line me-1"></i>Hapus
                </button>
            </div>

            <div class="row mb-3">
                <div class="col-md-2">
                    <label>Label <span class="text-danger">*</span></label>
                    <input type="text" name="sub_categories[${index}][label]" 
                           class="form-control" 
                           placeholder="e.g. a, b, c" required>
                    <small class="text-muted">Contoh: a, b, c</small>
                </div>

                <div class="col-md-8">
                    <label>Nama Sub Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="sub_categories[${index}][name]" 
                           class="form-control"
                           placeholder="e.g. Persyaratan Umum" required>
                    <small class="text-muted">Contoh: Persyaratan Umum</small>
                </div>

                <div class="col-md-2">
                    <label>Urutan <span class="text-danger">*</span></label>
                    <input type="number" name="sub_categories[${index}][order]" 
                           class="form-control"
                           value="${index + 1}"
                           min="0" required>
                </div>
            </div>

            <div class="questions-section border-top pt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="fw-bold"><i class="ri-question-line me-2"></i>Pertanyaan</label>
                    <button type="button" class="btn btn-info btn-sm add-question" data-subcat-index="${index}">
                        <i class="ri-add-line me-1"></i>Tambah Pertanyaan
                    </button>
                </div>

                <div class="questions-container">
                    <p class="text-muted small mb-0">Belum ada pertanyaan. Klik "Tambah Pertanyaan" untuk menambahkan.</p>
                </div>
            </div>
        </div>
    `;
}

// Template untuk Question baru
function getQuestionTemplate(subCatIndex, qIndex) {
    return `
        <div class="question-item bg-light p-2 rounded mb-2">
            <div class="row">
                <div class="col-md-2">
                    <label class="small">Nomor</label>
                    <input type="text" name="sub_categories[${subCatIndex}][questions][${qIndex}][number]" 
                           class="form-control form-control-sm"
                           placeholder="1.1" required>
                </div>

                <div class="col-md-7">
                    <label class="small">Pertanyaan</label>
                    <textarea name="sub_categories[${subCatIndex}][questions][${qIndex}][question]" 
                              class="form-control form-control-sm" 
                              rows="2" required></textarea>
                </div>

                <div class="col-md-2">
                    <label class="small">Urutan</label>
                    <input type="number" name="sub_categories[${subCatIndex}][questions][${qIndex}][order]" 
                           class="form-control form-control-sm"
                           value="${qIndex + 1}"
                           min="0" required>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-question w-100">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Add Sub Category
$('#addSubCategory').on('click', function() {
    const container = $('#subCategoriesContainer');
    container.find('p.text-muted').remove();
    container.append(getSubCategoryTemplate(subCategoryIndex));
    subCategoryIndex++;
    updateSubCategoryNumbers();
});

// Remove Sub Category
$(document).on('click', '.remove-sub-category', function() {
    if (confirm('Yakin ingin menghapus sub kategori ini beserta semua pertanyaannya?')) {
        $(this).closest('.sub-category-item').remove();
        updateSubCategoryNumbers();
        
        if ($('.sub-category-item').length === 0) {
            $('#subCategoriesContainer').html('<p class="text-muted text-center mb-0">Belum ada sub kategori. Klik "Tambah Sub Kategori" untuk memulai.</p>');
        }
    }
});

// Add Question
$(document).on('click', '.add-question', function() {
    const subCatIndex = $(this).data('subcat-index');
    const questionsContainer = $(this).closest('.questions-section').find('.questions-container');
    questionsContainer.find('p.text-muted').remove();
    
    const currentQuestionCount = questionsContainer.find('.question-item').length;
    questionsContainer.append(getQuestionTemplate(subCatIndex, currentQuestionCount));
});

// Remove Question
$(document).on('click', '.remove-question', function() {
    if (confirm('Yakin ingin menghapus pertanyaan ini?')) {
        const questionsContainer = $(this).closest('.questions-container');
        $(this).closest('.question-item').remove();
        
        if (questionsContainer.find('.question-item').length === 0) {
            questionsContainer.html('<p class="text-muted small mb-0">Belum ada pertanyaan. Klik "Tambah Pertanyaan" untuk menambahkan.</p>');
        }
    }
});

// Update numbering
function updateSubCategoryNumbers() {
    $('.sub-category-item').each(function(index) {
        $(this).find('.sub-cat-number').text(index + 1);
    });
}
</script>
@endpush