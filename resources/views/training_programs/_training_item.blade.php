<div class="training-item bg-light border rounded p-3 mb-3">
    <input type="hidden" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][id]" value="{{ $trainingItem->id ?? '' }}">
    
    <div class="d-flex justify-content-between align-items-center mb-2">
        <strong class="small text-info">
            <i class="ri-file-text-line me-1"></i>
            Training Item #<span class="training-item-number">{{ $tiIndex + 1 }}</span>
        </strong>
        <button type="button" class="btn btn-danger btn-sm remove-training-item">
            <i class="ri-delete-bin-line me-1"></i>Hapus
        </button>
    </div>

    {{-- Basic Info --}}
    <div class="row mb-2">
        <div class="col-md-1">
            <label class="small">No <span class="text-danger">*</span></label>
            <input type="text" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][number]" 
                   class="form-control form-control-sm" 
                   value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.number', $trainingItem->number ?? $tiIndex + 1) }}"
                   placeholder="1" required>
        </div>

        <div class="col-md-9">
            <label class="small">Nama Pelatihan <span class="text-danger">*</span></label>
            <input type="text" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][nama_pelatihan]" 
                   class="form-control form-control-sm" 
                   value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.nama_pelatihan', $trainingItem->nama_pelatihan ?? '') }}"
                   placeholder="e.g. Pengenalan Perusahaan" required>
        </div>

        <div class="col-md-2">
            <label class="small">Urutan <span class="text-danger">*</span></label>
            <input type="number" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][order]" 
                   class="form-control form-control-sm" 
                   value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.order', $trainingItem->order ?? $tiIndex + 1) }}"
                   min="0" required>
        </div>
    </div>

    {{-- Detail Fields --}}
    <div class="row mb-2">
        <div class="col-md-3">
            <label class="small">Peserta</label>
            <textarea name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][peserta]" 
                      class="form-control form-control-sm" 
                      rows="2" 
                      placeholder="e.g. Karyawan baru">{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.peserta', $trainingItem->peserta ?? '') }}</textarea>
        </div>

        <div class="col-md-3">
            <label class="small">Instruktur</label>
            <input type="text" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][instruktur]" 
                   class="form-control form-control-sm"
                   value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.instruktur', $trainingItem->instruktur ?? '') }}"
                   placeholder="e.g. Atasan ybs">
        </div>

        <div class="col-md-2">
            <label class="small">Metode</label>
            <input type="text" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][metode]" 
                   class="form-control form-control-sm"
                   value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.metode', $trainingItem->metode ?? '') }}"
                   placeholder="Presentasi">
        </div>

        <div class="col-md-2">
            <label class="small">Jadwal</label>
            <input type="text" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][jadwal]" 
                   class="form-control form-control-sm"
                   value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.jadwal', $trainingItem->jadwal ?? '') }}"
                   placeholder="Mulai kerja">
        </div>

        <div class="col-md-2">
            <label class="small">Metode Penilaian</label>
            <input type="text" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][metode_penilaian]" 
                   class="form-control form-control-sm"
                   value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.metode_penilaian', $trainingItem->metode_penilaian ?? '') }}"
                   placeholder="Pertanyaan">
        </div>
    </div>

    {{-- Details Section (a, b, c) --}}
    <div class="border-top pt-2 mt-2">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="fw-bold text-secondary">
                <i class="ri-list-unordered me-1"></i>Details (a, b, c)
            </small>
            <button type="button" class="btn btn-secondary btn-sm add-detail" 
                    data-main-index="{{ $mcIndex }}" 
                    data-sub-index="{{ $scIndex }}" 
                    data-item-index="{{ $tiIndex }}">
                <i class="ri-add-line me-1"></i>Tambah Detail
            </button>
        </div>

        <div class="details-container">
            @if(isset($trainingItem) && $trainingItem->details->count() > 0)
                @foreach($trainingItem->details as $dIndex => $detail)
                    <div class="detail-item bg-white p-2 rounded mb-1">
                        <input type="hidden" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][details][{{ $dIndex }}][id]" value="{{ $detail->id }}">
                        
                        <div class="row g-1">
                            <div class="col-md-1">
                                <input type="text" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][details][{{ $dIndex }}][letter]" 
                                       class="form-control form-control-sm" 
                                       value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.details.'.$dIndex.'.letter', $detail->letter) }}"
                                       placeholder="a" required>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][details][{{ $dIndex }}][content]" 
                                       class="form-control form-control-sm" 
                                       value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.details.'.$dIndex.'.content', $detail->content) }}"
                                       placeholder="Content" required>
                            </div>
                            <div class="col-md-1">
                                <input type="number" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][details][{{ $dIndex }}][order]" 
                                       class="form-control form-control-sm" 
                                       value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.details.'.$dIndex.'.order', $detail->order) }}"
                                       required>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-detail w-100">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted small mb-0">Belum ada detail.</p>
            @endif
        </div>
    </div>

    {{-- Metadata Section (Optional) --}}
    <div class="border-top pt-2 mt-2">
        <small class="fw-bold text-muted d-block mb-2">
            <i class="ri-information-line me-1"></i>Informasi Tambahan (Opsional)
        </small>
        
        <div class="row g-2">
            <div class="col-md-3">
                <label class="small">Tanggal Mulai</label>
                <input type="date" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][tanggal_mulai]" 
                       class="form-control form-control-sm"
                       value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.tanggal_mulai', optional($trainingItem->metadata ?? null)->tanggal_mulai) }}">
            </div>

            <div class="col-md-3">
                <label class="small">Tanggal Selesai</label>
                <input type="date" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][tanggal_selesai]" 
                       class="form-control form-control-sm"
                       value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.tanggal_selesai', optional($trainingItem->metadata ?? null)->tanggal_selesai) }}">
            </div>

            <div class="col-md-6">
                <label class="small">Lokasi</label>
                <input type="text" name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][lokasi]" 
                       class="form-control form-control-sm"
                       value="{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.lokasi', optional($trainingItem->metadata ?? null)->lokasi) }}"
                       placeholder="e.g. Ruang Meeting">
            </div>
        </div>

        <div class="row g-2 mt-1">
            <div class="col-md-12">
                <label class="small">Catatan</label>
                <textarea name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][catatan]" 
                          class="form-control form-control-sm" 
                          rows="2" 
                          placeholder="Catatan tambahan...">{{ old('main_categories.'.$mcIndex.'.sub_categories.'.$scIndex.'.training_items.'.$tiIndex.'.catatan', optional($trainingItem->metadata ?? null)->catatan) }}</textarea>
            </div>
        </div>
    </div>

    {{-- Images Section (Optional) --}}
    <div class="border-top pt-2 mt-2">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="fw-bold text-muted">
                <i class="ri-image-line me-1"></i>Dokumentasi Gambar
            </small>
            {{-- <button type="button" class="btn btn-secondary btn-sm add-image" 
                    data-main-index="{{ $mcIndex }}" 
                    data-sub-index="{{ $scIndex }}" 
                    data-item-index="{{ $tiIndex }}">
                <i class="ri-add-line me-1"></i>Tambah Gambar
            </button> --}}
        </div>

        <div class="images-container">
            <div class="image-inputs-{{ $mcIndex }}-{{ $scIndex }}-{{ $tiIndex }}"></div>
        </div>

        @if(isset($trainingItem) && $trainingItem->images->count() > 0)
            <div class="existing-images mt-2">
                <small class="fw-bold">Gambar Yang Sudah Ada:</small>
                <div class="row mt-1">
                    @foreach($trainingItem->images as $image)
                        <div class="col-md-2 mb-2">
                            <div class="card">
                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                     class="card-img-top" 
                                     style="height: 60px; object-fit: cover;">
                                <div class="card-body p-1">
                                    @if($image->caption)
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">{{ Str::limit($image->caption, 20) }}</small>
                                    @endif
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="main_categories[{{ $mcIndex }}][sub_categories][{{ $scIndex }}][training_items][{{ $tiIndex }}][remove_images][]" 
                                               value="{{ $image->id }}" 
                                               id="remove_img_{{ $image->id }}">
                                        <label class="form-check-label text-danger" for="remove_img_{{ $image->id }}" style="font-size: 0.7rem;">
                                            Hapus
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>