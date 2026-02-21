@if($errors->any())
    @foreach($errors->all() as $error)
        <div class="alert alert-danger py-2">{{ $error }}</div>
    @endforeach
@endif

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label fw-bold">Periode <span class="text-danger">*</span></label>
        <input type="text" name="periode" class="form-control"
               placeholder="e.g. September 2023"
               value="{{ old('periode', $kontrolGudang->periode ?? '') }}" required>
        <div class="form-text">Format: Nama Bulan + Tahun</div>
    </div>
    <div class="col-md-8">
        <label class="form-label fw-bold">Nama Gudang <span class="text-danger">*</span></label>
        <input type="text" name="nama_gudang" class="form-control"
               placeholder="e.g. Gudang Penyimpanan PT. Premiere Alkes Nusindo"
               value="{{ old('nama_gudang', $kontrolGudang->nama_gudang ?? '') }}" required>
    </div>
</div>