@if($errors->any())
    @foreach($errors->all() as $error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endforeach
@endif

<style>
    .cursor { cursor: pointer; }
    .list-group-item { border: 1px solid #dee2e6; transition: all 0.2s ease; }
    .list-group-item:hover { background-color: #f8f9fa; }
    .selected-count {
        background: #28a745; color: white; border-radius: 15px;
        padding: 2px 8px; font-size: 0.8em; margin-left: 10px;
    }
    .btn-add-products {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none; color: white; padding: 10px 20px;
        border-radius: 8px; transition: all 0.3s ease;
    }
    .btn-add-products:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,123,255,0.3); color: white;
    }
    .btn-add-products:disabled { background: #6c757d; transform: none; box-shadow: none; }
    .card-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white; border-radius: 8px 8px 0 0 !important; padding: 15px 20px;
    }
    .form-label-required::after { content: " *"; color: #dc3545; }
    .info-box {
        background: #f8f9fa; border-left: 4px solid #0d6efd;
        padding: 15px; border-radius: 4px; margin-bottom: 20px;
    }
    .product-detail-card {
        border: 1px solid #e0e0e0; border-radius: 8px;
        padding: 15px; margin-bottom: 15px; background: #fff;
        transition: all 0.3s ease;
    }
    .product-detail-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-color: #007bff; }
    .product-header {
        display: flex; justify-content: space-between; align-items: center;
        padding-bottom: 12px; border-bottom: 2px solid #f0f0f0; margin-bottom: 15px;
    }
    .product-title { font-weight: 600; font-size: 1.05rem; color: #2c3e50; }
    .product-meta { font-size: 0.82rem; color: #666; margin-top: 2px; }
    .detail-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px; margin-bottom: 15px;
    }
    .detail-item { display: flex; flex-direction: column; }
    .detail-label {
        font-size: 0.75rem; font-weight: 600; color: #666;
        margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .stock-section { background: #f8f9fa; padding: 12px; border-radius: 6px; margin-top: 10px; }
    .stock-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 10px; }
    .readonly-info {
        background: #e9ecef; border: 1px solid #dee2e6;
        border-radius: 4px; padding: 4px 8px; font-size: 0.85rem; color: #495057;
        min-height: 31px; display: flex; align-items: center;
    }
    .empty-state-modern {
        text-align: center; padding: 60px 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 12px;
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
                <span class="text-danger">**)</span> Stok disimpan dalam satuan dasar (PCS).
                Sistem otomatis menampilkan konversi ke BOX/LUSIN/dll berdasarkan data satuan produk.<br>
                <span class="text-danger">***)</span> Jumlah keluar &amp; retur dihitung otomatis dari riwayat transaksi.
            </p>
        </div>
    </div>
</div>

<!-- SECTION 1: Informasi Gudang -->
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
                <label class="form-label form-label-required">Nama Gudang / Kategori Obat</label>
                <input type="text" name="nama_gudang" class="form-control"
                       value="{{ old('nama_gudang', $gudang->nama_gudang ?? '') }}"
                       placeholder="e.g. Obat Sakit Kepala, Antibiotik, Alkes" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Lokasi</label>
                <input type="text" name="lokasi" class="form-control"
                       value="{{ old('lokasi', $gudang->lokasi ?? '') }}"
                       placeholder="e.g. Lantai 1, Gedung A">
            </div>
            <div class="col-md-6">
                <label class="form-label">Penanggung Jawab</label>
                <input type="text" name="penanggung_jawab" class="form-control"
                       value="{{ old('penanggung_jawab', $gudang->penanggung_jawab ?? '') }}"
                       placeholder="Nama penanggung jawab">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label form-label-required">Status</label>
                <select name="status" class="form-select" required>
                    <option value="Aktif"    {{ old('status', $gudang->status ?? 'Aktif') == 'Aktif'    ? 'selected' : '' }}>Aktif</option>
                    <option value="Nonaktif" {{ old('status', $gudang->status ?? '') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2"
                          placeholder="Keterangan tambahan">{{ old('keterangan', $gudang->keterangan ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 2: Detail Barang -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ri-box-3-line me-2"></i>Detail Stok Barang</h5>
            <button type="button" class="btn btn-add-products" id="btnOpenProductModal">
                <i class="ri-add-circle-line me-2"></i>
                Pilih Produk
                <span class="selected-count" id="selectedCount" style="display:none">0</span>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div id="productCardsContainer">

            @if(isset($gudang) && $gudang->details->count())
                @foreach($gudang->details as $index => $detail)
                @php
                    $jmlKeluar = $detail->jumlahKeluar();
                    $jmlRetur  = $detail->jumlahRetur();
                @endphp
                {{-- PERBAIKAN: tambah data-supplier-id pada card --}}
                <div class="product-detail-card"
                     data-produk-id="{{ $detail->produk_id }}"
                     data-supplier-id="{{ $detail->supplier_id ?? '' }}">
                    <input type="hidden" name="detail_id[]"  value="{{ $detail->id }}">
                    <input type="hidden" name="produk_id[]"  value="{{ $detail->produk_id }}">

                    <div class="product-header">
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-secondary card-number">#{{ $index + 1 }}</span>
                            <div>
                                <div class="product-title">
                                    {{ $detail->produk->nama_produk ?? '-' }}
                                    @if($detail->produk->merk)
                                        <small class="text-muted fw-normal">— {{ $detail->produk->merk }}</small>
                                    @endif
                                </div>
                                <div class="product-meta">
                                    <span class="badge bg-light text-dark border">{{ $detail->produk->jenis ?? '-' }}</span>
                                    <span class="ms-1 text-muted">NIE: {{ $detail->produk->nie ?? '-' }}</span>
                                    <span class="ms-1 text-muted">Kode: {{ $detail->produk->kode_produk ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($detail->isBelowMinimum())
                                <span class="badge bg-danger">Stok Menipis</span>
                            @endif
                            @if($detail->isNearExpiry())
                                <span class="badge bg-warning text-dark">Segera Kadaluarsa</span>
                            @endif
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-card">
                                <i class="ri-delete-bin-line"></i> Hapus
                            </button>
                        </div>
                    </div>

                    <div class="detail-grid">
                        <div class="detail-item">
                            <label class="detail-label">Supplier</label>
                            <select name="detail_supplier_id[]" class="form-select form-select-sm supplier-select">
                                <option value="">-- Tanpa Supplier --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}"
                                        {{ $detail->supplier_id == $sup->id ? 'selected' : '' }}>
                                        {{ $sup->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="detail-item">
                            <label class="detail-label">No Batch</label>
                            <input type="text" name="no_batch[]"
                                   class="form-control form-control-sm"
                                   value="{{ $detail->no_batch }}" placeholder="No Batch">
                        </div>

                        <div class="detail-item">
                            <label class="detail-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk[]"
                                   class="form-control form-control-sm"
                                   value="{{ $detail->tanggal_masuk?->format('Y-m-d') }}">
                        </div>

                        <div class="detail-item">
                            <label class="detail-label">Tanggal Produksi</label>
                            <input type="date" name="tanggal_produksi[]"
                                   class="form-control form-control-sm"
                                   value="{{ $detail->tanggal_produksi?->format('Y-m-d') }}">
                        </div>

                        <div class="detail-item">
                            <label class="detail-label">Tanggal Kadaluarsa</label>
                            <input type="date" name="tanggal_kadaluarsa[]"
                                   class="form-control form-control-sm"
                                   value="{{ $detail->tanggal_kadaluarsa?->format('Y-m-d') }}">
                        </div>

                        <div class="detail-item">
                            <label class="detail-label">Lokasi Rak</label>
                            <input type="text" name="lokasi_rak[]"
                                   class="form-control form-control-sm"
                                   value="{{ $detail->lokasi_rak }}" placeholder="A1-B2">
                        </div>
                    </div>

                    <div class="stock-section">
                        <div class="stock-grid">
                            <div class="detail-item">
                                <label class="detail-label text-primary">Stok (PCS) *</label>
                                <input type="number" name="stock_gudang[]"
                                       class="form-control form-control-sm"
                                       value="{{ $detail->stock_gudang }}" min="0" required>
                                <small class="text-muted">= {{ $detail->stok_dalam_satuan }}</small>
                            </div>

                            <div class="detail-item">
                                <label class="detail-label text-secondary">Jml Keluar (PCS)</label>
                                <div class="readonly-info">{{ number_format($jmlKeluar) }}</div>
                                <small class="text-muted">Dari riwayat transaksi</small>
                            </div>

                            <div class="detail-item">
                                <label class="detail-label text-secondary">Jml Retur (PCS)</label>
                                <div class="readonly-info">{{ number_format($jmlRetur) }}</div>
                                <small class="text-muted">Dari riwayat transaksi</small>
                            </div>

                            <div class="detail-item">
                                <label class="detail-label text-warning">Min Persediaan *</label>
                                <input type="number" name="min_persediaan[]"
                                       class="form-control form-control-sm"
                                       value="{{ $detail->min_persediaan }}" min="0" required>
                            </div>

                            <div class="detail-item">
                                <label class="detail-label text-success">Kondisi *</label>
                                <select name="kondisi[]" class="form-select form-select-sm">
                                    <option value="Baik"       {{ $detail->kondisi == 'Baik'       ? 'selected' : '' }}>Baik</option>
                                    <option value="Rusak"      {{ $detail->kondisi == 'Rusak'      ? 'selected' : '' }}>Rusak</option>
                                    <option value="Kadaluarsa" {{ $detail->kondisi == 'Kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="empty-state-modern" id="emptyState">
                    <div style="font-size:4rem; color:#6c757d" class="mb-3"><i class="ri-inbox-line"></i></div>
                    <h4 class="text-muted mb-3">Belum Ada Stok Barang</h4>
                    <p class="text-muted mb-4">Klik tombol <strong>"Pilih Produk"</strong> di atas untuk menambahkan produk ke gudang ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- MODAL PILIH PRODUK -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ri-search-line me-2"></i>Cari Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3 g-2">
                    <div class="col-md-4">
                        <select id="modalSupplierId" class="form-select form-select-sm">
                            <option value="">-- Semua / Tanpa Supplier --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->nama_supplier }}</option>
                            @endforeach
                        </select>
                        {{-- PERBAIKAN: tambah hint agar user tahu fungsi dropdown supplier di modal --}}
                        <small class="text-muted mt-1 d-block">
                            <i class="ri-information-line"></i>
                            Pilih supplier terlebih dahulu jika ingin menambahkan produk yang sama dari supplier berbeda.
                        </small>
                    </div>
                    <div class="col-md-8">
                        <div class="input-group input-group-sm">
                            <input type="text" id="searchProductInput" class="form-control"
                                   placeholder="Ketik nama produk / kode / merk / jenis...">
                            <button class="btn btn-primary" type="button" id="btnSearchProduct">
                                <i class="ri-search-line"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="searchResultsContainer" class="list-group" style="max-height:55vh;overflow:auto;">
                    <div class="text-center text-muted py-5">
                        <i class="ri-information-line"></i> Ketik minimal 2 huruf untuk mencari produk...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <small class="text-muted me-auto"><span id="modalSelectedCount">0</span> produk dipilih</small>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnAddSelectedProducts">
                    <i class="ri-check-line me-1"></i>Tambahkan Produk
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let selectedProducts = [];
    let cardCounter = {{ isset($gudang) ? $gudang->details->count() : 0 }};

    const suppliersData = @json($suppliers->map(fn($s) => ['id' => $s->id, 'nama' => $s->nama_supplier]));

    // ── Buka modal ──────────────────────────────────────
    $('#btnOpenProductModal').on('click', function () {
        // Reset semua state modal setiap kali dibuka
        selectedProducts = [];
        updateSelectedCount();
        $('#searchProductInput').val('');
        $('#modalSupplierId').val('');
        $('#searchResultsContainer').html(`
            <div class="text-center text-muted py-5">
                <i class="ri-information-line"></i> Ketik minimal 2 huruf untuk mencari produk...
            </div>
        `);
        $('#productModal').modal('show');
    });

    // ── Update counter badge ─────────────────────────────
    function updateSelectedCount() {
        const n = selectedProducts.length;
        $('#modalSelectedCount').text(n);
        $('#selectedCount').text(n).toggle(n > 0);
    }

    function updateCardNumbers() {
        $('#productCardsContainer .product-detail-card').each(function (i) {
            $(this).find('.card-number').text('#' + (i + 1));
        });
    }

    // ── Cari produk via API ──────────────────────────────
    function searchProducts(query) {
        const supplierId = $('#modalSupplierId').val();
        let url = `/api/gudang/search-products?q=${encodeURIComponent(query)}`;
        if (supplierId) url += `&supplier_id=${supplierId}`;

        $('#searchResultsContainer').html(`
            <div class="text-center text-muted py-5">
                <i class="ri-loader-4-line ri-spin"></i> Mencari...
            </div>
        `);

        $.get(url, function (data) {
            if (!data.length) {
                $('#searchResultsContainer').html(`
                    <div class="text-center text-muted py-5">
                        <i class="ri-error-warning-line"></i> Produk tidak ditemukan.
                    </div>
                `);
                return;
            }

            const currentSupplierId = $('#modalSupplierId').val();

            const html = data.map(p => {
                /*
                 * ============================================================
                 * PERBAIKAN UTAMA
                 * Cek duplikat berdasarkan kombinasi produk_id + supplier_id.
                 * Produk A dari Supplier X  ≠  Produk A dari Supplier Y
                 * Keduanya boleh masuk ke gudang yang sama.
                 * ============================================================
                 */
                const alreadyAdded = $(
                    `[data-produk-id="${p.id}"][data-supplier-id="${currentSupplierId}"]`
                ).length > 0;

                const checked    = selectedProducts.some(
                    s => s.id === p.id && s.supplierId === currentSupplierId
                );
                const satuanList = p.satuans.map(s => `${s.nama_satuan}(${s.konversi})`).join(' / ');

                return `
                    <label class="list-group-item d-flex justify-content-between align-items-center cursor
                                  ${alreadyAdded ? 'bg-light text-muted' : ''}">
                        <div>
                            <input type="checkbox" class="form-check-input me-2 search-product-checkbox"
                                data-id="${p.id}"
                                data-kode="${p.kode_produk}"
                                data-nama="${p.nama_produk}"
                                data-merk="${p.merk || ''}"
                                data-jenis="${p.jenis || ''}"
                                data-nie="${p.nie || ''}"
                                data-satuans='${JSON.stringify(p.satuans)}'
                                ${checked      ? 'checked'  : ''}
                                ${alreadyAdded ? 'disabled' : ''}>
                            <strong>${p.nama_produk}</strong>
                            ${p.merk ? `<span class="text-muted"> — ${p.merk}</span>` : ''}
                            <br>
                            <small class="text-muted">
                                ${p.jenis ? `<span class="badge bg-light text-dark border me-1">${p.jenis}</span>` : ''}
                                Kode: ${p.kode_produk} &nbsp;|&nbsp; NIE: ${p.nie || '-'} &nbsp;|&nbsp; Satuan: ${satuanList}
                            </small>
                        </div>
                        ${alreadyAdded ? '<span class="badge bg-success">Sudah ditambahkan</span>' : ''}
                    </label>
                `;
            }).join('');

            $('#searchResultsContainer').html(html);
        }).fail(function () {
            $('#searchResultsContainer').html(`
                <div class="text-center text-danger py-5">
                    <i class="ri-error-warning-line"></i> Gagal mengambil data.
                </div>
            `);
        });
    }

    $('#searchProductInput').on('input', function () {
        const q = $(this).val().trim();
        if (q.length < 2) {
            $('#searchResultsContainer').html(`
                <div class="text-center text-muted py-5">
                    <i class="ri-information-line"></i> Ketik minimal 2 huruf...
                </div>
            `);
            return;
        }
        searchProducts(q);
    });

    // Ketika supplier di modal diganti → reset pilihan & refresh hasil pencarian
    $('#modalSupplierId').on('change', function () {
        selectedProducts = [];
        updateSelectedCount();
        const q = $('#searchProductInput').val().trim();
        if (q.length >= 2) searchProducts(q);
    });

    // ── Checkbox pilih produk ────────────────────────────
    $(document).on('change', '.search-product-checkbox', function () {
        /*
         * ============================================================
         * PERBAIKAN:
         * Setiap item yang dicentang dicatat bersama supplierId
         * yang sedang aktif di dropdown modal.
         * Key unik = produk_id + supplierId
         * ============================================================
         */
        const currentSupplierId   = $('#modalSupplierId').val();
        const currentSupplierNama = $('#modalSupplierId option:selected').text().trim();

        const d = {
            id           : $(this).data('id'),
            kode         : $(this).data('kode'),
            nama         : $(this).data('nama'),
            merk         : $(this).data('merk'),
            jenis        : $(this).data('jenis'),
            nie          : $(this).data('nie'),
            satuans      : $(this).data('satuans'),
            supplierId   : currentSupplierId,
            supplierNama : currentSupplierId ? currentSupplierNama : '',
        };

        if ($(this).is(':checked')) {
            // Tambah hanya jika belum ada kombinasi produk + supplier ini
            if (!selectedProducts.some(p => p.id === d.id && p.supplierId === d.supplierId)) {
                selectedProducts.push(d);
            }
        } else {
            // Hapus hanya entry dengan kombinasi produk + supplier yang sama
            selectedProducts = selectedProducts.filter(
                p => !(p.id === d.id && p.supplierId === d.supplierId)
            );
        }
        updateSelectedCount();
    });

    // ── Build supplier dropdown options ─────────────────
    function supplierOptions(selectedId = '') {
        let opts = '<option value="">-- Tanpa Supplier --</option>';
        suppliersData.forEach(s => {
            opts += `<option value="${s.id}" ${String(s.id) === String(selectedId) ? 'selected' : ''}>${s.nama}</option>`;
        });
        return opts;
    }

    // ── Tambah produk terpilih ke card ───────────────────
    $('#btnAddSelectedProducts').on('click', function () {
        if (!selectedProducts.length) {
            alert('Pilih minimal 1 produk!');
            return;
        }

        $('#emptyState').remove();
        const today = new Date().toISOString().split('T')[0];

        selectedProducts.forEach(p => {
            /*
             * ============================================================
             * PERBAIKAN:
             * Guard duplikat cek data-produk-id + data-supplier-id.
             * Produk yang sama tapi supplier berbeda = tetap masuk.
             * ============================================================
             */
            if ($(`[data-produk-id="${p.id}"][data-supplier-id="${p.supplierId}"]`).length > 0) return;

            cardCounter++;
            const satuanInfo = p.satuans.map(s => `${s.nama_satuan}(${s.konversi})`).join(' / ');

            const cardHtml = `
                <div class="product-detail-card"
                     data-produk-id="${p.id}"
                     data-supplier-id="${p.supplierId}">
                    <input type="hidden" name="detail_id[]"  value="">
                    <input type="hidden" name="produk_id[]"  value="${p.id}">

                    <div class="product-header">
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-secondary card-number">#${cardCounter}</span>
                            <div>
                                <div class="product-title">
                                    ${p.nama}
                                    ${p.merk ? `<small class="text-muted fw-normal">— ${p.merk}</small>` : ''}
                                </div>
                                <div class="product-meta">
                                    ${p.jenis ? `<span class="badge bg-light text-dark border">${p.jenis}</span>` : ''}
                                    <span class="ms-1 text-muted">NIE: ${p.nie || '-'}</span>
                                    <span class="ms-1 text-muted">Kode: ${p.kode}</span>
                                    <span class="ms-1 text-muted">Satuan: ${satuanInfo}</span>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-card">
                            <i class="ri-delete-bin-line"></i> Hapus
                        </button>
                    </div>

                    <div class="detail-grid">
                        <div class="detail-item">
                            <label class="detail-label">Supplier</label>
                            {{--
                                PERBAIKAN:
                                Dropdown supplier di card langsung ter-set ke supplier
                                yang dipilih di modal. User masih bisa mengganti manual.
                            --}}
                            <select name="detail_supplier_id[]"
                                    class="form-select form-select-sm supplier-select">
                                ${supplierOptions(p.supplierId)}
                            </select>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">No Batch</label>
                            <input type="text" name="no_batch[]"
                                   class="form-control form-control-sm" placeholder="No Batch">
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk[]"
                                   class="form-control form-control-sm" value="${today}">
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Tanggal Produksi</label>
                            <input type="date" name="tanggal_produksi[]"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Tanggal Kadaluarsa</label>
                            <input type="date" name="tanggal_kadaluarsa[]"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">Lokasi Rak</label>
                            <input type="text" name="lokasi_rak[]"
                                   class="form-control form-control-sm" placeholder="A1-B2">
                        </div>
                    </div>

                    <div class="stock-section">
                        <div class="stock-grid">
                            <div class="detail-item">
                                <label class="detail-label text-primary">Stok (PCS) *</label>
                                <input type="number" name="stock_gudang[]"
                                       class="form-control form-control-sm" value="0" min="0" required>
                            </div>
                            <div class="detail-item">
                                <label class="detail-label text-warning">Min Persediaan *</label>
                                <input type="number" name="min_persediaan[]"
                                       class="form-control form-control-sm" value="0" min="0" required>
                            </div>
                            <div class="detail-item">
                                <label class="detail-label text-success">Kondisi *</label>
                                <select name="kondisi[]" class="form-select form-select-sm">
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
        });

        updateCardNumbers();
        selectedProducts = [];
        updateSelectedCount();
        $('#productModal').modal('hide');
    });

    // ── Hapus card ───────────────────────────────────────
    $(document).on('click', '.btn-remove-card', function () {
        if (confirm('Hapus produk ini dari daftar?')) {
            $(this).closest('.product-detail-card').remove();
            cardCounter = Math.max(0, cardCounter - 1);
            updateCardNumbers();

            if ($('#productCardsContainer .product-detail-card').length === 0) {
                $('#productCardsContainer').html(`
                    <div class="empty-state-modern" id="emptyState">
                        <div style="font-size:4rem; color:#6c757d" class="mb-3"><i class="ri-inbox-line"></i></div>
                        <h4 class="text-muted mb-3">Belum Ada Stok Barang</h4>
                        <p class="text-muted">Klik tombol <strong>"Pilih Produk"</strong> di atas.</p>
                    </div>
                `);
            }
        }
    });

    // ── Reset saat modal ditutup ─────────────────────────
    $('#productModal').on('hidden.bs.modal', function () {
        selectedProducts = [];
        updateSelectedCount();
    });

    $(document).on('change', '.supplier-select', function () {
        const newSupplierId = $(this).val();
        $(this).closest('.product-detail-card').attr('data-supplier-id', newSupplierId);
    });
});
</script>
@endpush