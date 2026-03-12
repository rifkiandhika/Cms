@if($errors->any())
    @foreach($errors->all() as $error)
        <div class="alert alert-danger py-2">{{ $error }}</div>
    @endforeach
@endif

<div class="row g-3">
    {{-- Tanggal --}}
    <div class="col-md-6">
        <label class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
        
        <input type="hidden" name="sop_id" value="{{ old('sop_id', request('sop_id') ?? $catatan->sop_id ?? '') }}">
        <input type="date" name="tanggal" class="form-control form-control-sm"
               value="{{ old('tanggal', isset($catatan) ? $catatan->tanggal?->format('Y-m-d') : '') }}" required>
    </div>

    {{-- Suhu Refrigerator --}}
    <div class="col-md-3">
        <label class="form-label fw-bold">Suhu Refrigerator (°C) <span class="text-danger">*</span></label>
        <input type="number" step="0.1" name="suhu_refrigerator" class="form-control form-control-sm"
               placeholder="e.g. 8.0"
               value="{{ old('suhu_refrigerator', $catatan->suhu_refrigerator ?? '') }}" required>
    </div>

    {{-- Suhu Ruangan --}}
    <div class="col-md-3">
        <label class="form-label fw-bold">Suhu Ruangan (°C) <span class="text-danger">*</span></label>
        <input type="number" step="0.1" name="suhu_ruangan" class="form-control form-control-sm"
               placeholder="e.g. 24.5"
               value="{{ old('suhu_ruangan', $catatan->suhu_ruangan ?? '') }}" required>
    </div>

    {{-- Kelembapan --}}
    <div class="col-md-4">
        <label class="form-label fw-bold">Kelembapan (%) <span class="text-danger">*</span></label>
        <input type="number" step="0.1" name="kelembapan" class="form-control form-control-sm"
               placeholder="e.g. 45"
               value="{{ old('kelembapan', $catatan->kelembapan ?? '') }}" required>
    </div>

    {{-- Kebersihan --}}
    <div class="col-md-4">
        <label class="form-label fw-bold">Kebersihan <span class="text-danger">*</span></label>
        <select name="kebersihan" class="form-select form-select-sm" required>
            <option value="1" {{ old('kebersihan', $catatan->kebersihan ?? '') == '1' ? 'selected' : '' }}>
                ✔ Bersih
            </option>
            <option value="0" {{ old('kebersihan', $catatan->kebersihan ?? '') == '0' ? 'selected' : '' }}>
                ✘ Tidak Bersih
            </option>
        </select>
    </div>

    {{-- Keamanan --}}
    <div class="col-md-4">
        <label class="form-label fw-bold">Keamanan <span class="text-danger">*</span></label>
        <select name="keamanan" class="form-select form-select-sm" required>
            <option value="1" {{ old('keamanan', $catatan->keamanan ?? '') == '1' ? 'selected' : '' }}>
                ✔ Aman
            </option>
            <option value="0" {{ old('keamanan', $catatan->keamanan ?? '') == '0' ? 'selected' : '' }}>
                ✘ Tidak Aman
            </option>
        </select>
    </div>
</div>