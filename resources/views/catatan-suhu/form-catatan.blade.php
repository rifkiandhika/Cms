@if($errors->any())
    @foreach($errors->all() as $error)
        <div class="alert alert-danger py-2">{{ $error }}</div>
    @endforeach
@endif

<div class="row g-3">
    {{-- Tanggal --}}
    <div class="col-md-6">
        <label class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
        <input type="date" name="tanggal" class="form-control"
               value="{{ old('tanggal', isset($catatan) ? $catatan->tanggal?->format('Y-m-d') : '') }}" required>
    </div>

    {{-- Kelembapan --}}
    <div class="col-md-6">
        <label class="form-label fw-bold">Kelembapan (%) <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="number" step="0.1" min="0" max="100" name="kelembapan" class="form-control"
                   placeholder="e.g. 45"
                   value="{{ old('kelembapan', $catatan->kelembapan ?? '') }}" required>
            <span class="input-group-text">%</span>
        </div>
    </div>

    {{-- Suhu Refrigerator --}}
    <div class="col-md-6">
        <label class="form-label fw-bold">Suhu Refrigerator <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="number" step="0.1" name="suhu_refrigerator" class="form-control"
                   placeholder="e.g. 8.0"
                   value="{{ old('suhu_refrigerator', $catatan->suhu_refrigerator ?? '') }}" required>
            <span class="input-group-text">°C</span>
        </div>
    </div>

    {{-- Suhu Ruangan --}}
    <div class="col-md-6">
        <label class="form-label fw-bold">Suhu Ruangan <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="number" step="0.1" name="suhu_ruangan" class="form-control"
                   placeholder="e.g. 24.5"
                   value="{{ old('suhu_ruangan', $catatan->suhu_ruangan ?? '') }}" required>
            <span class="input-group-text">°C</span>
        </div>
    </div>

    {{-- Kebersihan --}}
    <div class="col-md-6">
        <label class="form-label fw-bold">Kebersihan <span class="text-danger">*</span></label>
        <select name="kebersihan" class="form-select" required>
            <option value="" disabled {{ old('kebersihan', $catatan->kebersihan ?? '') === '' ? 'selected' : '' }}>-- Pilih --</option>
            <option value="1" {{ old('kebersihan', isset($catatan) ? ($catatan->kebersihan ? 1 : 0) : '') == 1 ? 'selected' : '' }}>
                ✔ Bersih
            </option>
            <option value="0" {{ old('kebersihan', isset($catatan) ? ($catatan->kebersihan ? 1 : 0) : '') == 0 && isset($catatan) ? 'selected' : '' }}>
                ✘ Tidak Bersih
            </option>
        </select>
    </div>

    {{-- Keamanan --}}
    <div class="col-md-6">
        <label class="form-label fw-bold">Keamanan <span class="text-danger">*</span></label>
        <select name="keamanan" class="form-select" required>
            <option value="" disabled {{ old('keamanan', $catatan->keamanan ?? '') === '' ? 'selected' : '' }}>-- Pilih --</option>
            <option value="1" {{ old('keamanan', isset($catatan) ? ($catatan->keamanan ? 1 : 0) : '') == 1 ? 'selected' : '' }}>
                ✔ Aman
            </option>
            <option value="0" {{ old('keamanan', isset($catatan) ? ($catatan->keamanan ? 1 : 0) : '') == 0 && isset($catatan) ? 'selected' : '' }}>
                ✘ Tidak Aman
            </option>
        </select>
    </div>
</div>