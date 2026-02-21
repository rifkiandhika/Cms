@if($errors->any())
    @foreach($errors->all() as $error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endforeach
@endif

<style>
    .cursor { cursor: pointer; }
    .modal-dialog-scrollable { height: 90vh; }
    .modal-body { max-height: 70vh; overflow-y: auto; }
    
    .list-group-item { border: 1px solid #dee2e6; transition: all 0.2s ease; }
    .list-group-item:hover { background-color: #f8f9fa; }
    
    .selected-count {
        background: #28a745;
        color: white;
        border-radius: 15px;
        padding: 2px 8px;
        font-size: 0.8em;
        margin-left: 10px;
    }
    
    .btn-add-products {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .btn-add-products:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        color: white;
    }
    
    .btn-add-products:disabled {
        background: #6c757d;
        transform: none;
        box-shadow: none;
    }
    
    .card-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px 8px 0 0 !important;
        padding: 15px 20px;
    }
    
    .form-label-required::after {
        content: " *";
        color: #dc3545;
    }
    
    .info-box {
        background: #f8f9fa;
        border-left: 4px solid #0d6efd;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    
    .section-divider {
        border-top: 2px solid #e9ecef;
        margin: 30px 0;
        position: relative;
    }
    
    .section-divider::before {
        content: attr(data-title);
        position: absolute;
        top: -12px;
        left: 20px;
        background: white;
        padding: 0 10px;
        color: #6c757d;
        font-weight: 600;
        font-size: 14px;
    }

    /* Compact Table Styles */
    .product-detail-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background: #fff;
        transition: all 0.3s ease;
    }

    .product-detail-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-color: #007bff;
    }

    .product-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 12px;
        border-bottom: 2px solid #f0f0f0;
        margin-bottom: 15px;
    }

    .product-title {
        font-weight: 600;
        font-size: 1.1rem;
        color: #2c3e50;
    }

    .product-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .badge-obat { background: #e3f2fd; color: #1976d2; }
    .badge-alkes { background: #e8f5e9; color: #388e3c; }
    .badge-reagensia { background: #fff3e0; color: #f57c00; }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin-bottom: 15px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
    }

    .detail-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #666;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-input {
        width: 100%;
    }

    .stock-section {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 6px;
        margin-top: 10px;
    }

    .stock-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 10px;
    }

    .btn-remove-card {
        padding: 6px 16px;
        font-size: 0.875rem;
    }

    .empty-state-modern {
        text-align: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 12px;
    }

    .empty-state-icon {
        font-size: 4rem;
        color: #6c757d;
        margin-bottom: 20px;
    }
</style>

<!-- Info Box -->
<div class="info-box">
    <div class="d-flex align-items-center">
        <i class="ri-information-line fs-4 me-3 text-primary"></i>
        <div>
            <strong>Keterangan:</strong>
            <p class="mb-0 mt-1 small">
                <span class="text-danger">*)</span> Field wajib diisi<br>
                <span class="text-danger">**)</span> Pilih supplier terlebih dahulu sebelum menambah barang<br>
                <span class="text-danger">***)</span> Data yang sudah ditambahkan dapat dihapus dengan tombol hapus
            </p>
        </div>
    </div>
</div>

<!-- Section: Informasi Gudang -->
<div class="card shadow-sm mb-4">
    <div class="card-header-custom">
        <h5 class="mb-0"><i class="ri-store-2-line me-2"></i>INFORMASI GUDANG</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label form-label-required">Kode Gudang</label>
                <input type="text" name="kode_gudang" class="form-control" 
                       value="{{ old('kode_gudang', $gudang->kode_gudang ?? '') }}"
                       placeholder="e.g. GDG-001" required>
            </div>

            <div class="col-md-6">
                <label class="form-label form-label-required">Nama Gudang</label>
                <input type="text" name="nama_gudang" class="form-control"
                       value="{{ old('nama_gudang', $gudang->nama_gudang ?? '') }}"
                       placeholder="e.g. Gudang Pusat" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label form-label-required">Pilih Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-select cursor" required>
                    <option value="" hidden>-- Pilih Supplier --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            {{ old('supplier_id', $gudang->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->nama_supplier }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Lokasi</label>
                <input type="text" name="lokasi" class="form-control"
                       value="{{ old('lokasi', $gudang->lokasi ?? '') }}"
                       placeholder="e.g. Lantai 1, Gedung A">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Penanggung Jawab</label>
                <input type="text" name="penanggung_jawab" class="form-control"
                       value="{{ old('penanggung_jawab', $gudang->penanggung_jawab ?? '') }}"
                       placeholder="Nama penanggung jawab">
            </div>

            <div class="col-md-6">
                <label class="form-label form-label-required">Status</label>
                <select name="status" class="form-select" required>
                    <option value="Aktif" {{ old('status', $gudang->status ?? 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Nonaktif" {{ old('status', $gudang->status ?? '') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2" 
                          placeholder="Keterangan tambahan tentang gudang">{{ old('keterangan', $gudang->keterangan ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

<!-- Section Divider -->
<div class="section-divider" data-title="DETAIL BARANG"></div>

<!-- Section: Detail Barang -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ri-box-3-line me-2"></i>Detail Barang</h5>
            <button type="button" class="btn btn-add-products" id="btnOpenProductModal" 
                    {{ isset($gudang) && $gudang->supplier_id ? '' : 'disabled' }}>
                <i class="ri-add-circle-line me-2"></i>
                Pilih Barang
                <span class="selected-count" id="selectedCount" style="display: none;">0</span>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div id="productCardsContainer">
            {{-- Data edit --}}
            @if(isset($gudang) && $gudang->details->count())
                @foreach($gudang->details as $index => $detail)
                    <div class="product-detail-card" data-id="{{ $detail->barang_id }}" data-type="{{ $detail->barang_type }}">
                        <!-- Hidden Fields -->
                        <input type="hidden" name="barang_id[]" value="{{ $detail->barang_id }}">
                        <input type="hidden" name="barang_type[]" value="{{ $detail->barang_type }}">
                        
                        <!-- Product Header -->
                        <div class="product-header">
                            <div class="d-flex align-items-center gap-3">
                                <div class="product-number">
                                    <span class="badge bg-secondary">#{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <div class="product-title">{{ $detail->produk->nama_produk ?? 'Produk' }}</div>
                                    <small class="text-muted">{{ $detail->produk->jenis }}</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="product-badge badge-{{ $detail->produk->jenis }}">
                                    {{ ucfirst($detail->produk->jenis) }}
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-card">
                                    <i class="ri-delete-bin-line"></i> Hapus
                                </button>
                            </div>
                        </div>

                        <!-- Detail Grid -->
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label class="detail-label">Kode Produk</label>
                                <input type="text" name="kode_produk[]" class="form-control form-control-sm detail-input" 
                                       value="{{ $detail->kode_produk }}" placeholder="Kode">
                            </div>

                            <div class="detail-item">
                                <label class="detail-label">Nama Dagangan/Merk</label>
                                <input type="text" name="nama_dagangan[]" class="form-control form-control-sm detail-input" 
                                       value="{{ $detail->nama_dagangan }}" placeholder="Nama Dagangan">
                            </div>

                            <div class="detail-item">
                                <label class="detail-label">Nomor Izin Edar</label>
                                <input type="text" name="nomor_izin_edar[]" class="form-control form-control-sm detail-input" 
                                       value="{{ $detail->nomor_izin_edar }}" placeholder="No. Izin">
                            </div>

                            <div class="detail-item">
                                <label class="detail-label">Tipe</label>
                                <input type="text" name="tipe[]" class="form-control form-control-sm detail-input" 
                                       value="{{ $detail->tipe }}" placeholder="Tipe">
                            </div>

                            <div class="detail-item">
                                <label class="detail-label">Ukuran</label>
                                <input type="text" name="ukuran[]" class="form-control form-control-sm detail-input" 
                                       value="{{ $detail->ukuran }}" placeholder="Ukuran">
                            </div>

                            <div class="detail-item">
                                <label class="detail-label">Kemasan</label>
                                <input type="text" name="kemasan[]" class="form-control form-control-sm detail-input" 
                                       value="{{ $detail->kemasan }}" placeholder="Kemasan">
                            </div>

                            <div class="detail-item">
                                <label class="detail-label">Satuan</label>
                                <input type="text" name="satuan[]" class="form-control form-control-sm detail-input" 
                                       value="{{ $detail->satuan }}" placeholder="Satuan">
                            </div>

                            <div class="detail-item">
                                <label class="detail-label">Satuan Lain</label>
                                <input type="text" name="satuan_lain[]" class="form-control form-control-sm detail-input" 
                                       value="{{ $detail->satuan_lain }}" placeholder="Satuan Lain">
                            </div>

                            <div class="detail-item">
                                <label class="detail-label">No Batch</label>
                                <input type="text" name="no_batch[]" class="form-control form-control-sm detail-input" 
                                       value="{{ $detail->no_batch }}" placeholder="No Batch">
                            </div>

                            <div class="detail-item">
                                <label class="detail-label">Kode Barcode</label>
                                <input type="text" name="kode_barcode[]" class="form-control form-control-sm detail-input" 
                                       value="{{ $detail->kode_barcode }}" placeholder="Barcode">
                            </div>

                            <div class="detail-item">
                                <label class="detail-label">Tanggal Produksi</label>
                                <input type="date" name="tanggal_produksi[]" class="form-control form-control-sm detail-input" 
                                       value="{{ $detail->tanggal_produksi }}">
                            </div>

                            <div class="detail-item">
                                <label class="detail-label">Lokasi Rak</label>
                                <input type="text" name="lokasi_rak[]" class="form-control form-control-sm detail-input" 
                                       value="{{ $detail->lokasi_rak }}" placeholder="A1-B2">
                            </div>
                        </div>

                        <!-- Stock Section -->
                        <div class="stock-section">
                            <div class="stock-grid">
                                <div class="detail-item">
                                    <label class="detail-label text-primary">Stock Gudang *</label>
                                    <input type="number" name="stock_gudang[]" class="form-control form-control-sm detail-input" 
                                           value="{{ $detail->stock_gudang }}" min="0" required>
                                </div>

                                <div class="detail-item">
                                    <label class="detail-label">Jumlah Keluar</label>
                                    <input type="number" name="jumlah_keluar[]" class="form-control form-control-sm detail-input" 
                                           value="{{ $detail->jumlah_keluar }}" min="0" readonly>
                                </div>

                                <div class="detail-item">
                                    <label class="detail-label">Jumlah Retur</label>
                                    <input type="number" name="jumlah_retur[]" class="form-control form-control-sm detail-input" 
                                           value="{{ $detail->jumlah_retur }}" min="0" readonly>
                                </div>

                                <div class="detail-item">
                                    <label class="detail-label text-warning">Min Persediaan *</label>
                                    <input type="number" name="min_persediaan[]" class="form-control form-control-sm detail-input" 
                                           value="{{ $detail->min_persediaan }}" min="0" required>
                                </div>

                                <div class="detail-item">
                                    <label class="detail-label">Tanggal Masuk</label>
                                    <input type="date" name="tanggal_masuk[]" class="form-control form-control-sm detail-input" 
                                           value="{{ $detail->tanggal_masuk }}">
                                </div>

                                <div class="detail-item">
                                    <label class="detail-label text-success">Kondisi *</label>
                                    <select name="kondisi[]" class="form-select form-select-sm detail-input">
                                        <option value="Baik" {{ $detail->kondisi == 'Baik' ? 'selected' : '' }}>Baik</option>
                                        <option value="Rusak" {{ $detail->kondisi == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                                        <option value="Kadaluarsa" {{ $detail->kondisi == 'Kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state-modern" id="emptyState">
                    <div class="empty-state-icon">
                        <i class="ri-inbox-line"></i>
                    </div>
                    <h4 class="text-muted mb-3">Belum Ada Detail Barang</h4>
                    <p class="text-muted mb-4">Silakan pilih supplier terlebih dahulu, kemudian klik tombol "Pilih Barang" untuk menambahkan barang ke gudang.</p>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <i class="ri-arrow-up-line text-primary fs-4"></i>
                        <span class="text-primary fw-bold">Klik tombol "Pilih Barang" di atas</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Pilih Barang -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="productModalLabel">
          <i class="ri-search-line me-2"></i> Cari Barang dari Supplier
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Search Box -->
        <div class="input-group mb-3">
          <input type="text" id="searchProductInput" class="form-control" placeholder="Ketik nama / jenis / judul barang...">
          <button class="btn btn-outline-light bg-primary" type="button" id="btnSearchProduct">
            <i class="ri-search-line"></i>
          </button>
        </div>

        <!-- Hasil Pencarian -->
        <div id="searchResultsContainer" class="list-group" style="max-height:60vh;overflow:auto;">
          <div class="text-center text-muted py-5">
            <i class="ri-information-line"></i> Ketik minimal 2 huruf untuk mencari barang...
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="me-auto">
          <small class="text-muted"><span id="modalSelectedCount">0</span> barang dipilih</small>
        </div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Batal
        </button>
        <button type="button" class="btn btn-primary" id="btnAddSelectedProducts">
          <i class="ri-check-line me-1"></i> Tambahkan Barang
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  let selectedProducts = [];
  let supplierId = null;
  let cardCounter = {{ isset($gudang) && $gudang->details->count() ? $gudang->details->count() : 0 }};

  // Aktifkan tombol "Pilih Barang" hanya jika supplier dipilih
  $('#supplier_id').on('change', function () {
    supplierId = $(this).val();
    $('#btnOpenProductModal').prop('disabled', !supplierId);
  });

  // Buka modal pencarian
  $('#btnOpenProductModal').on('click', function () {
    supplierId = $('#supplier_id').val();
    if (!supplierId) {
      alert('Silakan pilih supplier terlebih dahulu!');
      return;
    }
    $('#searchProductInput').val('');
    $('#searchResultsContainer').html(`
      <div class="text-center text-muted py-5">
        <i class="ri-information-line"></i> Ketik minimal 2 huruf untuk mencari barang...
      </div>
    `);
    $('#productModal').modal('show');
  });

  // Fungsi untuk memperbarui jumlah terpilih
  function updateSelectedCount() {
    const count = selectedProducts.length;
    $('#modalSelectedCount').text(count);
    $('#selectedCount').text(count).toggle(count > 0);
  }

  // Fungsi untuk update nomor urut
  function updateCardNumbers() {
    $('#productCardsContainer .product-detail-card').each(function(index) {
      $(this).find('.product-number .badge').text('#' + (index + 1));
    });
  }

  // Fungsi untuk mencari barang dari supplier
  function searchSupplierProducts(query) {
    if (!supplierId) return;

    $('#searchResultsContainer').html(`
      <div class="text-center text-muted py-5">
        <i class="ri-loader-4-line ri-spin"></i> Mencari data...
      </div>
    `);

    $.get(`/api/supplier/${supplierId}/search-products?q=${query}`, function (data) {
      if (data.length === 0) {
        $('#searchResultsContainer').html(`
          <div class="text-center text-muted py-5">
            <i class="ri-error-warning-line"></i> Tidak ditemukan hasil.
          </div>
        `);
        return;
      }

      let html = data.map(d => {
        const isChecked = selectedProducts.some(p => p.id === d.id && p.type === d.barang_type);
        const isAlreadyInCard = $(`.product-detail-card[data-id="${d.id}"][data-type="${d.barang_type}"]`).length > 0;
        return `
          <label class="list-group-item d-flex justify-content-between align-items-center cursor ${isAlreadyInCard ? 'bg-light text-muted' : ''}">
            <div>
              <input type="checkbox" class="form-check-input me-2 search-product-checkbox"
                data-id="${d.id}" 
                data-nama="${d.nama}"
                data-judul="${d.judul || ''}" 
                data-jenis="${d.jenis || ''}"
                data-exp_date="${d.exp_date || ''}"
                data-type="${d.barang_type}"
                data-kode_produk="${d.kode_produk}"
                data-satuan="${d.satuan}"
                ${isChecked ? 'checked' : ''} 
                ${isAlreadyInCard ? 'disabled' : ''}>
              <strong>${d.nama}</strong>
              <small class="text-muted">(${d.jenis}${d.judul ? ' • ' + d.judul : ''})</small>
            </div>
            ${isAlreadyInCard ? '<span class="badge bg-success">Sudah ditambahkan</span>' : ''}
          </label>
        `;
      }).join('');

      $('#searchResultsContainer').html(html);
    }).fail(function() {
      $('#searchResultsContainer').html(`
        <div class="text-center text-danger py-5">
          <i class="ri-error-warning-line"></i> Terjadi kesalahan saat mengambil data.
        </div>
      `);
    });
  }

  // Jalankan pencarian ketika user mengetik
  $('#searchProductInput').on('input', function () {
    const query = $(this).val().trim();
    if (query.length < 2) {
      $('#searchResultsContainer').html(`
        <div class="text-center text-muted py-5">
          <i class="ri-information-line"></i> Ketik minimal 2 huruf untuk mencari barang...
        </div>
      `);
      return;
    }
    searchSupplierProducts(query);
  });

  // Checkbox pilih barang
  $(document).on('change', '.search-product-checkbox', function () {
    const productData = {
      id: $(this).data('id'),
      nama: $(this).data('nama'),
      judul: $(this).data('judul'),
      jenis: $(this).data('jenis'),
      exp_date: $(this).data('exp_date'),
      kode_produk: $(this).data('kode_produk'),
      satuan: $(this).data('satuan'),
      type: $(this).data('type')
    };

    if ($(this).is(':checked')) {
      if (!selectedProducts.some(p => p.id === productData.id && p.type === productData.type)) {
        selectedProducts.push(productData);
      }
    } else {
      selectedProducts = selectedProducts.filter(p => !(p.id === productData.id && p.type === productData.type));
    }
    updateSelectedCount();
  });

  // Tambahkan produk terpilih ke cards
  $('#btnAddSelectedProducts').on('click', function () {
    if (selectedProducts.length === 0) {
      alert('Silakan pilih minimal 1 barang!');
      return;
    }

    $('#emptyState').remove();
    
    selectedProducts.forEach(p => {
      if ($(`.product-detail-card[data-id="${p.id}"][data-type="${p.jenis}"]`).length === 0) {
        cardCounter++;
        const today = new Date().toISOString().split('T')[0];
        
        const badgeClass = p.jenis === 'obat' ? 'badge-obat' : (p.jenis === 'alkes' ? 'badge-alkes' : 'badge-reagensia');
        
        const cardHtml = `
          <div class="product-detail-card" data-id="${p.id}" data-type="${p.jenis}">
            <!-- Hidden Fields -->
            <input type="hidden" name="barang_id[]" value="${p.id}">
            <input type="hidden" name="barang_type[]" value="${p.jenis}">
            
            <!-- Product Header -->
            <div class="product-header">
              <div class="d-flex align-items-center gap-3">
                <div class="product-number">
                  <span class="badge bg-secondary">#${cardCounter}</span>
                </div>
                <div>
                  <div class="product-title">${p.nama}</div>
                  <small class="text-muted">${p.jenis}</small>
                </div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <span class="product-badge ${badgeClass}">
                  ${p.jenis.charAt(0).toUpperCase() + p.jenis.slice(1)}
                </span>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-card">
                  <i class="ri-delete-bin-line"></i> Hapus
                </button>
              </div>
            </div>

            <!-- Detail Grid -->
            <div class="detail-grid">
              <div class="detail-item">
                <label class="detail-label">Kode Produk</label>
                <input type="text" name="kode_produk[]" class="form-control form-control-sm detail-input" placeholder="Kode" value="${p.kode_produk}">
              </div>

              <div class="detail-item">
                <label class="detail-label">Nama Dagangan/Merk</label>
                <input type="text" name="nama_dagangan[]" class="form-control form-control-sm detail-input" placeholder="Nama Dagangan">
              </div>

              <div class="detail-item">
                <label class="detail-label">Nomor Izin Edar</label>
                <input type="text" name="nomor_izin_edar[]" class="form-control form-control-sm detail-input" placeholder="No. Izin">
              </div>

              <div class="detail-item">
                <label class="detail-label">Tipe</label>
                <input type="text" name="tipe[]" class="form-control form-control-sm detail-input" placeholder="Tipe" value="${p.jenis}" disabled>
              </div>

              <div class="detail-item">
                <label class="detail-label">Ukuran</label>
                <input type="text" name="ukuran[]" class="form-control form-control-sm detail-input" placeholder="Ukuran">
              </div>

              <div class="detail-item">
                <label class="detail-label">Kemasan</label>
                <input type="text" name="kemasan[]" class="form-control form-control-sm detail-input" placeholder="Kemasan">
              </div>

              <div class="detail-item">
                <label class="detail-label">Satuan</label>
                <input type="text" name="satuan[]" class="form-control form-control-sm detail-input" placeholder="Satuan">
              </div>

              <div class="detail-item">
                <label class="detail-label">Satuan Lain</label>
                <input type="text" name="satuan_lain[]" class="form-control form-control-sm detail-input" placeholder="Satuan Lain">
              </div>

              <div class="detail-item">
                <label class="detail-label">No Batch</label>
                <input type="text" name="no_batch[]" class="form-control form-control-sm detail-input" placeholder="No Batch">
              </div>

              <div class="detail-item">
                <label class="detail-label">Kode Barcode</label>
                <input type="text" name="kode_barcode[]" class="form-control form-control-sm detail-input" placeholder="Barcode">
              </div>

              <div class="detail-item">
                <label class="detail-label">Tanggal Produksi</label>
                <input type="date" name="tanggal_produksi[]" class="form-control form-control-sm detail-input">
              </div>

              <div class="detail-item">
                <label class="detail-label">Lokasi Rak</label>
                <input type="text" name="lokasi_rak[]" class="form-control form-control-sm detail-input" placeholder="A1-B2">
              </div>
            </div>

            <!-- Stock Section -->
            <div class="stock-section">
              <div class="stock-grid">
                <div class="detail-item">
                  <label class="detail-label text-primary">Stock Gudang *</label>
                  <input type="number" name="stock_gudang[]" class="form-control form-control-sm detail-input" value="0" min="0" required>
                </div>

                <div class="detail-item">
                  <label class="detail-label">Jumlah Keluar</label>
                  <input type="number" name="jumlah_keluar[]" class="form-control form-control-sm detail-input" value="0" min="0" readonly>
                </div>

                <div class="detail-item">
                  <label class="detail-label">Jumlah Retur</label>
                  <input type="number" name="jumlah_retur[]" class="form-control form-control-sm detail-input" value="0" min="0" readonly>
                </div>

                <div class="detail-item">
                  <label class="detail-label text-warning">Min Persediaan *</label>
                  <input type="number" name="min_persediaan[]" class="form-control form-control-sm detail-input" value="0" min="0" required>
                </div>

                <div class="detail-item">
                  <label class="detail-label">Tanggal Masuk</label>
                  <input type="date" name="tanggal_masuk[]" class="form-control form-control-sm detail-input" value="${today}">
                </div>

                <div class="detail-item">
                  <label class="detail-label text-success">Kondisi *</label>
                  <select name="kondisi[]" class="form-select form-select-sm detail-input">
                    <option value="Baik" selected>Baik</option>
                    <option value="Rusak">Rusak</option>
                    <option value="Kadaluarsa">Kadaluarsa</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        `;
        
        $('#productCardsContainer').append(cardHtml);
      }
    });
    
    updateCardNumbers();
    selectedProducts = [];
    updateSelectedCount();
    $('#productModal').modal('hide');
  });

  // Hapus card
  $(document).on('click', '.btn-remove-card', function () {
    if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
      $(this).closest('.product-detail-card').remove();
      cardCounter--;
      updateCardNumbers();

      // Tampilkan empty state jika tidak ada card
      if ($('#productCardsContainer .product-detail-card').length === 0) {
        $('#productCardsContainer').html(`
          <div class="empty-state-modern" id="emptyState">
            <div class="empty-state-icon">
              <i class="ri-inbox-line"></i>
            </div>
            <h4 class="text-muted mb-3">Belum Ada Detail Barang</h4>
            <p class="text-muted mb-4">Silakan pilih supplier terlebih dahulu, kemudian klik tombol "Pilih Barang" untuk menambahkan barang ke gudang.</p>
            <div class="d-flex justify-content-center align-items-center gap-2">
              <i class="ri-arrow-up-line text-primary fs-4"></i>
              <span class="text-primary fw-bold">Klik tombol "Pilih Barang" di atas</span>
            </div>
          </div>
        `);
      }
    }
  });

  // Reset ketika modal ditutup
  $('#productModal').on('hidden.bs.modal', function () {
    selectedProducts = [];
    updateSelectedCount();
  });

  // Set supplier_id saat halaman load (untuk edit mode)
  @if(isset($gudang) && $gudang->supplier_id)
    supplierId = "{{ $gudang->supplier_id }}";
    $('#btnOpenProductModal').prop('disabled', false);
  @endif
});
</script>
@endpush