<div class="sub-category-item bg-white border rounded p-3 mb-3" data-index="{{ $scIndex }}">
    <input type="hidden" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][id]" value="{{ $subCategory->id ?? '' }}">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0 text-success">
            <i class="ri-folder-2-line me-2"></i>
            Sub Category #<span class="sub-cat-number">{{ $scIndex + 1 }}</span>
        </h6>
        <button type="button" class="btn btn-danger btn-sm remove-sub-category">
            <i class="ri-delete-bin-line me-1"></i>Hapus
        </button>
    </div>

    <div class="row mb-3">
        <div class="col-md-2">
            <label class="small fw-bold">Letter <span class="text-danger">*</span></label>
            <input type="text" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][letter]" 
                   class="form-control form-control-sm" 
                   value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.letter', $subCategory->letter ?? '') }}"
                   placeholder="A" required>
            <small class="text-muted">A, B, C</small>
        </div>

        <div class="col-md-8">
            <label class="small fw-bold">Nama Sub Category <span class="text-danger">*</span></label>
            <input type="text" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][name]" 
                   class="form-control form-control-sm"
                   value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.name', $subCategory->name ?? '') }}"
                   placeholder="e.g. ORIENTASI UMUM" required>
        </div>

        <div class="col-md-2">
            <label class="small fw-bold">Urutan <span class="text-danger">*</span></label>
            <input type="number" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][order]" 
                   class="form-control form-control-sm"
                   value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.order', $subCategory->order ?? $scIndex + 1) }}"
                   min="0" required>
        </div>
    </div>

    {{-- Training Items Section --}}
    <div class="training-items-section border-top pt-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="small fw-bold text-info">
                <i class="ri-list-check me-2"></i>Training Items (1, 2, 3)
            </label>
            <button type="button" class="btn btn-info btn-sm add-training-item" 
                    data-main-index="{{ $mcIndex }}" 
                    data-sub-index="{{ $scIndex }}">
                <i class="ri-add-line me-1"></i>Tambah Item
            </button>
        </div>

        <div class="training-items-container">
            @if(isset($subCategory) && $subCategory->trainingItems->count() > 0)
                @foreach($subCategory->trainingItems as $tiIndex => $trainingItem)
                    @include('training_programs._training_item', [
                        'mcIndex' => $mcIndex,
                        'scIndex' => $scIndex,
                        'tiIndex' => $tiIndex,
                        'trainingItem' => $trainingItem
                    ])
                @endforeach
            @else
                <p class="text-muted small mb-0">Belum ada training item. Klik "Tambah Item".</p>
            @endif
        </div>
    </div>
</div>