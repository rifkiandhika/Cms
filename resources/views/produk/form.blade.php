<h5 class="mb-3">Data Produk</h5>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Kode Produk</label>
        <input type="text" 
               name="kode_produk" 
               class="form-control bg-light" 
               placeholder="Auto-generate"
               value="{{ old('kode_produk', $produk->kode_produk ?? '') }}"
               readonly>
        <small class="text-muted">Kode produk akan dibuat otomatis</small>
    </div>
    <div class="col-md-6">
        <label>NIE (Nomor Izin Edar) <span class="text-danger">*</span></label>
        <input type="text" 
               name="nie" 
               class="form-control" 
               placeholder="e.g. DKL1234567890123"
               value="{{ old('nie', $produk->nie ?? '') }}"
               required>
        @error('nie')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Nama Produk <span class="text-danger">*</span></label>
        <input type="text" 
               name="nama_produk" 
               class="form-control" 
               placeholder="e.g. Paracetamol 500mg"
               value="{{ old('nama_produk', $produk->nama_produk ?? '') }}"
               required>
        @error('nama_produk')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label>Merk</label>
        <input type="text" 
               name="merk" 
               class="form-control" 
               placeholder="e.g. Kimia Farma"
               value="{{ old('merk', $produk->merk ?? '') }}">
        @error('merk')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Jenis <span class="text-danger">*</span></label>
        <select name="jenis" class="form-select" required>
            <option value="" hidden>-- Pilih Jenis --</option>
            @foreach($jenis as $j)
                <option value="{{ $j->nama_jenis }}" 
                    {{ old('jenis', $produk->jenis ?? '') == $j->nama_jenis ? 'selected' : '' }}>
                    {{ $j->nama_jenis }}
                </option>
            @endforeach
        </select>
        @error('jenis')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="col-md-6">
        <label>Satuan <span class="text-danger">*</span></label>
        <select name="satuan" class="form-select" required>
            <option value="" hidden>-- Pilih satuan --</option>
            @foreach($satuan as $item)
                <option value="{{ $item->nama_satuan }}" 
                    {{ old('satuan', $produk->satuan ?? '') == $item->nama_satuan ? 'selected' : '' }}>
                    {{ $item->nama_satuan }}
                </option>
            @endforeach
        </select>
        @error('satuan')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Harga Beli <span class="text-danger">*</span></label>
        <input type="text"
               name="harga_beli"
               class="form-control format-rupiah"
               value="{{ old('harga_beli', isset($produk) ? number_format($produk->harga_beli, 0, ',', '.') : '') }}"
               placeholder="e.g. 50.000"
               required>
        @error('harga_beli')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label>Harga Jual <span class="text-danger">*</span></label>
        <input type="text"
               name="harga_jual"
               class="form-control format-rupiah"
               value="{{ old('harga_jual', isset($produk) ? number_format($produk->harga_jual, 0, ',', '.') : '') }}"
               placeholder="e.g. 75.000"
               required>
        @error('harga_jual')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" required>
            <option value="" hidden>-- Pilih Status --</option>
            <option value="aktif" {{ old('status', $produk->status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ old('status', $produk->status ?? '') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        @error('status')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label>Deskripsi</label>
        <textarea 
            class="form-control" 
            name="deskripsi" 
            rows="3" 
            placeholder="Keterangan tambahan tentang produk">{{ old('deskripsi', $produk->deskripsi ?? '') }}</textarea>
        @error('deskripsi')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

@push('scripts')
{{-- Format Rupiah --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rupiahInputs = document.querySelectorAll('.format-rupiah');

        rupiahInputs.forEach(input => {
            input.addEventListener('input', function (e) {
                // Hapus semua karakter non-digit
                let value = this.value.replace(/\D/g, '');

                // Format angka jadi ribuan
                this.value = new Intl.NumberFormat('id-ID').format(value);
            });

            // Saat form disubmit, ubah ke angka murni agar backend tidak bingung
            input.form?.addEventListener('submit', function () {
                rupiahInputs.forEach(inp => {
                    inp.value = inp.value.replace(/\./g, '');
                });
            });
        });
    });
</script>
@endpush