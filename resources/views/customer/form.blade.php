@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Terdapat kesalahan:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Kode Customer <span class="text-danger">*</span></label>
        <input type="text" name="kode_customer" class="form-control @error('kode_customer') is-invalid @enderror"
               value="{{ old('kode_customer', $customer->kode_customer ?? '') }}"
               placeholder="e.g. CUST001" required>
        @error('kode_customer')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Nama Customer <span class="text-danger">*</span></label>
        <input type="text" name="nama_customer" class="form-control @error('nama_customer') is-invalid @enderror"
               value="{{ old('nama_customer', $customer->nama_customer ?? '') }}"
               placeholder="e.g. RS Harapan Sehat" required>
        @error('nama_customer')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Tipe Customer <span class="text-danger">*</span></label>
        <select name="tipe_customer" class="form-select @error('tipe_customer') is-invalid @enderror" required>
            <option value="">-- Pilih Tipe Customer --</option>
            <option value="rumah_sakit" {{ old('tipe_customer', $customer->tipe_customer ?? '') == 'rumah_sakit' ? 'selected' : '' }}>Rumah Sakit</option>
            <option value="klinik" {{ old('tipe_customer', $customer->tipe_customer ?? '') == 'klinik' ? 'selected' : '' }}>Klinik</option>
            <option value="laboratorium" {{ old('tipe_customer', $customer->tipe_customer ?? '') == 'laboratorium' ? 'selected' : '' }}>Laboratorium</option>
            <option value="apotek" {{ old('tipe_customer', $customer->tipe_customer ?? '') == 'apotek' ? 'selected' : '' }}>Apotek</option>
            <option value="lainnya" {{ old('tipe_customer', $customer->tipe_customer ?? '') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
        </select>
        @error('tipe_customer')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="aktif" {{ old('status', $customer->status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ old('status', $customer->status ?? '') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Nama Kontak</label>
        <input type="text" name="nama_kontak" class="form-control @error('nama_kontak') is-invalid @enderror"
               value="{{ old('nama_kontak', $customer->nama_kontak ?? '') }}"
               placeholder="e.g. Budi Santoso">
        @error('nama_kontak')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $customer->email ?? '') }}"
               placeholder="e.g. kontak@customer.com">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Telepon</label>
        <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror"
               value="{{ old('telepon', $customer->telepon ?? '') }}"
               placeholder="e.g. 0812-3456-7890">
        @error('telepon')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                  rows="3" placeholder="e.g. Jl. Sudirman No. 123">{{ old('alamat', $customer->alamat ?? '') }}</textarea>
        @error('alamat')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Kota</label>
        <input type="text" name="kota" class="form-control @error('kota') is-invalid @enderror"
               value="{{ old('kota', $customer->kota ?? '') }}"
               placeholder="e.g. Jakarta">
        @error('kota')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Provinsi</label>
        <input type="text" name="provinsi" class="form-control @error('provinsi') is-invalid @enderror"
               value="{{ old('provinsi', $customer->provinsi ?? '') }}"
               placeholder="e.g. DKI Jakarta">
        @error('provinsi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">NPWP</label>
        <input type="text" name="npwp" class="form-control @error('npwp') is-invalid @enderror"
               value="{{ old('npwp', $customer->npwp ?? '') }}"
               placeholder="e.g. 01.234.567.8-901.000">
        @error('npwp')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Izin Operasional</label>
        <input type="text" name="izin_operasional" class="form-control @error('izin_operasional') is-invalid @enderror"
               value="{{ old('izin_operasional', $customer->izin_operasional ?? '') }}"
               placeholder="e.g. 123/SIK/2024">
        @error('izin_operasional')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>