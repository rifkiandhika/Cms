@extends('layouts.app')

@section('title', 'Buat Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('po.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Buat PO {{ ucfirst($type) }}</li>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
    .select2-container {
        width: 100% !important;
    }
    .batch-info {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
    .stock-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
    }
    .item-row {
        animation: fadeIn 0.3s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="app-body">
    {{-- Alert Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('po.store') }}" method="POST" id="formPO">
        @csrf
        <input type="hidden" name="tipe_po" value="{{ $type }}">
        <input type="hidden" name="unit_pemohon" value="{{ $type === 'penjualan' ? 'gudang' : 'gudang' }}">
        <input type="hidden" name="id_unit_pemohon" value="{{ auth()->user()->id_karyawan ?? '' }}">

        <div class="row">
            <!-- Left Column - Form -->
            <div class="col-xl-8">
                <!-- Informasi Umum -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-information-line me-2"></i>Informasi Purchase Order
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            @if($type === 'penjualan')
                                <strong>PO Penjualan:</strong> Penjualan barang dari <strong>Gudang</strong> ke <strong>Customer</strong><br>
                                <small class="text-muted">
                                    <i class="ri-checkbox-circle-line me-1"></i> Memerlukan persetujuan Kepala Gudang dan Kasir<br>
                                    <i class="ri-checkbox-circle-line me-1"></i> Stok akan dikurangi dari Gudang setelah pengiriman
                                </small>
                            @else
                                <strong>PO Pembelian:</strong> Pembelian barang dari <strong>Supplier</strong> ke <strong>Gudang</strong><br>
                                <small class="text-muted">
                                    <i class="ri-checkbox-circle-line me-1"></i> Memerlukan persetujuan Kepala Gudang dan Kasir<br>
                                    <i class="ri-checkbox-circle-line me-1"></i> Stok akan ditambah ke Gudang setelah diterima<br>
                                    <i class="ri-checkbox-circle-line me-1"></i> <strong>Harga dan jenis diambil dari detail supplier</strong>
                                </small>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-user-line me-1"></i> Pemohon
                                </label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-building-line me-1"></i> Unit Pemohon
                                </label>
                                <input type="text" class="form-control" value="Gudang" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                @if($type === 'penjualan')
                                    <label class="form-label fw-semibold">
                                        <i class="ri-user-line me-1"></i> Customer <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('id_customer') is-invalid @enderror" 
                                            name="id_customer" id="relasi" required>
                                        <option value="">-- Pilih Customer --</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('id_customer') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->nama_customer }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_customer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @else
                                    <label class="form-label fw-semibold">
                                        <i class="ri-store-line me-1"></i> Supplier <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('id_supplier') is-invalid @enderror" 
                                            name="id_supplier" id="relasi" required>
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('id_supplier') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->nama_supplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_supplier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-percent-line me-1"></i> Pajak (%)
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                        class="form-control" 
                                        name="pajak_persen" 
                                        id="pajak_persen"
                                        value="{{ old('pajak_persen', 0) }}" 
                                        min="0" 
                                        max="100"
                                        step="0.01"
                                        placeholder="0">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Nilai pajak: <span id="nilai_pajak_display">Rp 0</span></small>
                                <input type="hidden" name="pajak" id="pajak_value">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="ri-chat-3-line me-1"></i> Catatan
                            </label>
                            <textarea class="form-control @error('catatan_pemohon') is-invalid @enderror" 
                                      name="catatan_pemohon" 
                                      rows="3" 
                                      placeholder="Tambahkan catatan untuk PO ini...">{{ old('catatan_pemohon') }}</textarea>
                            @error('catatan_pemohon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Item PO -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="ri-shopping-cart-line me-2"></i>Item Purchase Order
                            </h5>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">
                                <i class="ri-add-line me-1"></i>Tambah Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="itemTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Produk <span class="text-danger">*</span></th>
                                        <th width="120">Harga</th>
                                        <th width="100">Qty <span class="text-danger">*</span></th>
                                        <th width="150" class="text-end">Subtotal</th>
                                        <th width="80" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemTableBody">
                                    <!-- Items will be added here dynamically -->
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">Total:</th>
                                        <th class="text-end" id="totalHarga">Rp 0</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end">Pajak:</th>
                                        <th class="text-end" id="totalPajak">Rp 0</th>
                                        <th></th>
                                    </tr>
                                    <tr class="table-primary">
                                        <th colspan="4" class="text-end">Grand Total:</th>
                                        <th class="text-end" id="grandTotal">Rp 0</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Summary & Actions -->
            <div class="col-xl-4">
                <!-- Summary Card -->
                <div class="card shadow-sm border-0 mb-4 position-sticky" style="top: 20px;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="ri-calculator-line me-2"></i>Ringkasan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Item:</span>
                            <strong id="summaryItemCount">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Quantity:</span>
                            <strong id="summaryTotalQty">0</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <strong id="summarySubtotal">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Pajak:</span>
                            <strong id="summaryPajak">Rp 0</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Grand Total:</span>
                            <h5 class="text-success mb-0" id="summaryGrandTotal">Rp 0</h5>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="ri-save-line me-1"></i> Simpan PO
                            </button>
                            <a href="{{ route('po.index') }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="ri-information-line text-info me-2"></i>Informasi
                        </h6>
                        <ul class="small mb-0">
                            <li class="mb-2">Pastikan semua item yang dipilih sudah benar</li>
                            <li class="mb-2">Quantity yang diisi adalah jumlah yang diminta</li>
                            @if($type === 'penjualan')
                                <li class="mb-2"><strong class="text-success">Produk ditampilkan per batch dengan stock gudang</strong></li>
                                <li class="mb-2 text-success"><strong>PO Penjualan memerlukan approval Kepala Gudang dan Kasir</strong></li>
                                <li class="mb-2 text-success"><strong>Stok akan dikurangi dari Gudang setelah pengiriman</strong></li>
                            @else
                                <li class="mb-2"><strong class="text-primary">Harga dan jenis otomatis dari Detail Supplier</strong></li>
                                <li class="mb-2">PO Pembelian memerlukan approval dari Kepala Gudang dan Kasir</li>
                                <li class="mb-2">Stok akan ditambah ke Gudang setelah barang diterima</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let itemCounter = 0;
    const produkData = Object.values(@json($produkList));
    const isPenjualan = {{ $type === 'penjualan' ? 'true' : 'false' }};

    // Filter produk by supplier (untuk pembelian)
    @if($type === 'pembelian')
    const relasiSelect = document.getElementById('relasi');
    let filteredProduk = [];

    relasiSelect.addEventListener('change', function() {
        const supplierId = this.value;
        
        if (supplierId) {
            filteredProduk = produkData.filter(p => p.supplier_id === supplierId);
            console.log('Filtered produk:', filteredProduk); // Debug
        } else {
            filteredProduk = [];
        }
        
        // Clear existing items
        document.getElementById('itemTableBody').innerHTML = '';
        itemCounter = 0;
        calculateTotal();
    });
    @else
    // Untuk penjualan, semua produk dari gudang tersedia
    let filteredProduk = produkData;
    @endif

    function addItem() {
        const relasiValue = document.getElementById('relasi').value;
        
        if (!relasiValue) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: isPenjualan ? 'Pilih customer terlebih dahulu' : 'Pilih supplier terlebih dahulu'
            });
            return;
        }

        if (filteredProduk.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Tidak ada produk tersedia',
                footer: isPenjualan ? 'Pastikan gudang sudah memiliki stock barang' : 'Supplier ini belum memiliki produk'
            });
            return;
        }

        itemCounter++;
        const tbody = document.getElementById('itemTableBody');
        const row = document.createElement('tr');
        row.className = 'item-row';
        row.id = `item-${itemCounter}`;

        row.innerHTML = `
            <td class="text-center">${itemCounter}</td>
            <td>
                <select class="form-select form-select-sm select2-produk"
                    name="items[${itemCounter}][id_produk]"
                    id="produk-${itemCounter}"
                    onchange="updatePrice(${itemCounter})" required>
                    <option value="">-- Pilih Produk --</option>
                </select>
                ${!isPenjualan ? `<input type="hidden" name="items[${itemCounter}][jenis]" id="jenis-${itemCounter}">` : ''}
                <div class="batch-info" id="batch-info-${itemCounter}"></div>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm text-end"
                    id="harga-${itemCounter}" readonly value="0">
                <input type="hidden" name="items[${itemCounter}][harga]"
                    id="harga-val-${itemCounter}" value="0">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm"
                    name="items[${itemCounter}][qty_diminta]"
                    id="qty-${itemCounter}"
                    min="1" value="1"
                    onchange="calculateSubtotal(${itemCounter})" required>
                <small class="text-muted" id="max-qty-${itemCounter}"></small>
            </td>
            <td class="text-end">
                <strong id="subtotal-${itemCounter}">Rp 0</strong>
                <input type="hidden" id="subtotal-val-${itemCounter}" value="0">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger"
                    onclick="removeItem(${itemCounter})">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </td>
        `;

        tbody.appendChild(row);
        initializeSelect2(itemCounter);
    }

    function initializeSelect2(itemId) {
        const selectElement = $(`#produk-${itemId}`);
        
        // Populate options
        filteredProduk.forEach((p) => {
            let optionText = '';
            let optionHtml = '';
            
            if (isPenjualan) {
                // Penjualan: Show batch info dan stock gudang
                optionText = `${p.nama} - Batch: ${p.no_batch} (Stock: ${p.stock_gudang})`;
                optionHtml = `
                    <div>
                        <strong>${p.nama}</strong> ${p.merk ? `- ${p.merk}` : ''}
                        <br>
                        <small class="text-muted">
                            <span class="badge bg-info stock-badge">Batch: ${p.no_batch}</span>
                            <span class="badge bg-success stock-badge">Stock: ${p.stock_gudang}</span>
                            ${p.tanggal_kadaluarsa !== '-' ? `<span class="badge bg-warning stock-badge text-dark">Exp: ${p.tanggal_kadaluarsa}</span>` : ''}
                        </small>
                    </div>
                `;
            } else {
                // ✅ Pembelian: Standard display dengan info jenis
                optionText = `${p.nama} - ${p.merk || ''} (${p.satuan})`;
                optionHtml = `
                    <div>
                        <strong>${p.nama}</strong> - ${p.merk || ''} (${p.satuan})
                        <br>
                        <small class="text-muted">
                            <span class="badge bg-primary stock-badge">Jenis: ${p.jenis}</span>
                            <span class="badge bg-success stock-badge">Harga: Rp ${formatRupiah(p.harga_beli)}</span>
                        </small>
                    </div>
                `;
            }
            
            const newOption = new Option(optionText, p.id, false, false);
            $(newOption).data('produk', p);
            selectElement.append(newOption);
        });

        // Initialize Select2
        selectElement.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Produk --',
            allowClear: true,
            templateResult: formatProduk,
            templateSelection: formatProdukSelection
        });
    }

    function formatProduk(produk) {
        if (!produk.id) return produk.text;
        
        const data = $(produk.element).data('produk');
        if (!data) return produk.text;

        if (isPenjualan) {
            return $(`
                <div>
                    <strong>${data.nama}</strong> ${data.merk ? `- ${data.merk}` : ''}
                    <br>
                    <small class="text-muted">
                        <span class="badge bg-info stock-badge">Batch: ${data.no_batch}</span>
                        <span class="badge bg-success stock-badge">Stock: ${data.stock_gudang}</span>
                        ${data.tanggal_kadaluarsa !== '-' ? `<span class="badge bg-warning stock-badge text-dark">Exp: ${data.tanggal_kadaluarsa}</span>` : ''}
                    </small>
                </div>
            `);
        } else {
            return $(`
                <div>
                    <strong>${data.nama}</strong> - ${data.merk || ''} (${data.satuan})
                    <br>
                    <small class="text-muted">
                        <span class="badge bg-primary stock-badge">Jenis: ${data.jenis}</span>
                        <span class="badge bg-success stock-badge">Harga: Rp ${formatRupiah(data.harga_beli)}</span>
                    </small>
                </div>
            `);
        }
    }

    function formatProdukSelection(produk) {
        if (!produk.id) return produk.text;
        const data = $(produk.element).data('produk');
        if (!data) return produk.text;
        
        if (isPenjualan) {
            return `${data.nama} - Batch: ${data.no_batch}`;
        } else {
            return `${data.nama} - ${data.jenis}`;
        }
    }

    function updatePrice(itemId) {
        const selectElement = $(`#produk-${itemId}`);
        const data = selectElement.find(':selected').data('produk');

        if (!data) return;

        let harga = 0;
        
        if (isPenjualan) {
            // Penjualan: gunakan harga_jual
            harga = parseFloat(data.harga_jual || 0);
            
            // Tampilkan batch info
            document.getElementById(`batch-info-${itemId}`).innerHTML = `
                <span class="badge bg-info stock-badge">Batch: ${data.no_batch}</span>
                <span class="badge bg-success stock-badge">Stock: ${data.stock_gudang}</span>
                <span class="badge bg-primary stock-badge">Harga: Rp ${formatRupiah(harga)}</span>
            `;

            const qtyInput = document.getElementById(`qty-${itemId}`);
            qtyInput.max = data.stock_gudang;
            document.getElementById(`max-qty-${itemId}`).textContent = `Max: ${data.stock_gudang}`;
        } else {
            // ✅ Pembelian: gunakan harga_beli dari detail_supplier dan set jenis
            harga = parseFloat(data.harga_beli || 0);
            
            // ✅ Set jenis dari detail_supplier
            const jenisInput = document.getElementById(`jenis-${itemId}`);
            if (jenisInput && data.jenis) {
                jenisInput.value = data.jenis;
                console.log(`Set jenis for item ${itemId}:`, data.jenis); // Debug
            }
            
            // Tampilkan info produk
            document.getElementById(`batch-info-${itemId}`).innerHTML = `
                <span class="badge bg-primary stock-badge">Jenis: ${data.jenis || 'lainnya'}</span>
                <span class="badge bg-success stock-badge">Harga: Rp ${formatRupiah(harga)}</span>
                <span class="badge bg-info stock-badge">${data.satuan || 'pcs'}</span>
            `;
        }

        document.getElementById(`harga-${itemId}`).value = formatRupiah(harga);
        document.getElementById(`harga-val-${itemId}`).value = harga;
        calculateSubtotal(itemId);
    }

    function calculateSubtotal(itemId) {
        const qtyInput = document.getElementById(`qty-${itemId}`);
        if (!qtyInput) return;

        let qty = parseInt(qtyInput.value || 0);

        // Validasi stock untuk penjualan
        if (isPenjualan) {
            const selectElement = $(`#produk-${itemId}`);
            const selectedOption = selectElement.find(':selected');
            const data = selectedOption.data('produk');

            if (data && qty > data.stock_gudang) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Quantity Melebihi Stock',
                    text: `Stock tersedia: ${data.stock_gudang}`
                });

                qty = data.stock_gudang;
                qtyInput.value = qty;
            }
        }

        const hargaInput = document.getElementById(`harga-val-${itemId}`);
        if (!hargaInput) return;

        const harga = parseFloat(hargaInput.value || 0);
        const subtotal = harga * qty;

        document.getElementById(`subtotal-${itemId}`).textContent = 'Rp ' + formatRupiah(subtotal);
        document.getElementById(`subtotal-val-${itemId}`).value = subtotal;

        calculateTotal();
    }

    function removeItem(itemId) {
        const totalRows = document.querySelectorAll('#itemTableBody tr').length;
        
        if (totalRows <= 1) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Dapat Menghapus',
                text: 'Minimal harus ada 1 item dalam Purchase Order'
            });
            return;
        }

        Swal.fire({
            title: 'Hapus Item?',
            text: 'Item akan dihapus dari PO',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const row = document.getElementById(`item-${itemId}`);
                row.remove();
                calculateTotal();
                renumberRows();
            }
        });
    }

    function renumberRows() {
        const rows = document.querySelectorAll('#itemTableBody tr');
        
        rows.forEach((row, index) => {
            const newNumber = index + 1;
            const oldId = row.id.split('-')[1];
            
            row.querySelector('td:first-child').textContent = newNumber;
            row.id = `item-${newNumber}`;
            
            const selectProduk = row.querySelector(`#produk-${oldId}`);
            if (selectProduk) {
                selectProduk.id = `produk-${newNumber}`;
                selectProduk.name = `items[${newNumber}][id_produk]`;
            }
            
            const jenisInput = row.querySelector(`#jenis-${oldId}`);
            if (jenisInput) {
                jenisInput.id = `jenis-${newNumber}`;
                jenisInput.name = `items[${newNumber}][jenis]`;
            }
            
            const hargaDisplay = row.querySelector(`#harga-${oldId}`);
            if (hargaDisplay) hargaDisplay.id = `harga-${newNumber}`;
            
            const hargaVal = row.querySelector(`#harga-val-${oldId}`);
            if (hargaVal) {
                hargaVal.id = `harga-val-${newNumber}`;
                hargaVal.name = `items[${newNumber}][harga]`;
            }
            
            const qtyInput = row.querySelector(`#qty-${oldId}`);
            if (qtyInput) {
                qtyInput.id = `qty-${newNumber}`;
                qtyInput.name = `items[${newNumber}][qty_diminta]`;
                qtyInput.setAttribute('onchange', `calculateSubtotal(${newNumber})`);
            }
            
            const maxQty = row.querySelector(`#max-qty-${oldId}`);
            if (maxQty) maxQty.id = `max-qty-${newNumber}`;
            
            const subtotalDisplay = row.querySelector(`#subtotal-${oldId}`);
            if (subtotalDisplay) subtotalDisplay.id = `subtotal-${newNumber}`;
            
            const subtotalVal = row.querySelector(`#subtotal-val-${oldId}`);
            if (subtotalVal) subtotalVal.id = `subtotal-val-${newNumber}`;
            
            const batchInfo = row.querySelector(`#batch-info-${oldId}`);
            if (batchInfo) batchInfo.id = `batch-info-${newNumber}`;
            
            const deleteBtn = row.querySelector('button[onclick^="removeItem"]');
            if (deleteBtn) deleteBtn.setAttribute('onclick', `removeItem(${newNumber})`);
            
            const select2Element = $(row).find('.select2-produk');
            if (select2Element.length) {
                const selectedValue = select2Element.val();
                const selectedData = select2Element.find(':selected').data('produk');
                
                select2Element.select2('destroy');
                select2Element.attr('id', `produk-${newNumber}`);
                select2Element.attr('onchange', `updatePrice(${newNumber})`);
                
                initializeSelect2AfterRenumber(newNumber, selectedValue, selectedData);
            }
        });
        
        itemCounter = rows.length;
    }

    function initializeSelect2AfterRenumber(itemId, selectedValue = null, selectedData = null) {
        const selectElement = $(`#produk-${itemId}`);
        
        selectElement.find('option:not(:first)').remove();
        
        filteredProduk.forEach((p) => {
            let optionText = '';
            
            if (isPenjualan) {
                optionText = `${p.nama} - Batch: ${p.no_batch} (Stock: ${p.stock_gudang})`;
            } else {
                optionText = `${p.nama} - ${p.merk || ''} (${p.satuan})`;
            }
            
            const newOption = new Option(optionText, p.id, false, p.id == selectedValue);
            $(newOption).data('produk', p);
            selectElement.append(newOption);
        });
        
        selectElement.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Produk --',
            allowClear: true,
            templateResult: formatProduk,
            templateSelection: formatProdukSelection
        });
        
        if (selectedValue) {
            selectElement.val(selectedValue).trigger('change');
        }
    }

    function calculateTotal() {
        let itemCount = 0;
        let totalQty = 0;
        let total = 0;

        const rows = document.querySelectorAll('#itemTableBody tr');

        rows.forEach(row => {
            const qtyInput = row.querySelector('input[id^="qty-"]');
            if (!qtyInput) return;

            const qty = parseInt(qtyInput.value || 0);
            if (qty > 0) {
                itemCount++;
                totalQty += qty;
            }

            const itemId = qtyInput.id.split('-')[1];
            const subtotalInput = document.getElementById(`subtotal-val-${itemId}`);
            if (subtotalInput) {
                total += parseFloat(subtotalInput.value || 0);
            }
        });

        const pajakPersen = parseFloat(document.getElementById('pajak_persen')?.value || 0);
        const pajakValue = (total * pajakPersen) / 100;
        const grandTotal = total + pajakValue;

        const pajakValueInput = document.getElementById('pajak_value');
        if (pajakValueInput) {
            pajakValueInput.value = pajakValue;
        }

        // Update summary
        document.getElementById('summaryItemCount').textContent = itemCount;
        document.getElementById('summaryTotalQty').textContent = totalQty;
        document.getElementById('totalHarga').textContent = 'Rp ' + formatRupiah(total);
        document.getElementById('grandTotal').textContent = 'Rp ' + formatRupiah(grandTotal);
        document.getElementById('totalPajak').textContent = 'Rp ' + formatRupiah(pajakValue);
        document.getElementById('summarySubtotal').textContent = 'Rp ' + formatRupiah(total);
        document.getElementById('summaryGrandTotal').textContent = 'Rp ' + formatRupiah(grandTotal);
        document.getElementById('summaryPajak').textContent = 'Rp ' + formatRupiah(pajakValue);
        
        if (document.getElementById('nilai_pajak_display')) {
            document.getElementById('nilai_pajak_display').textContent = 'Rp ' + formatRupiah(pajakValue);
        }
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    // Event listener for pajak
    document.getElementById('pajak_persen')?.addEventListener('input', calculateTotal);

    // Form validation
    document.getElementById('formPO').addEventListener('submit', function(e) {
        const itemCount = document.querySelectorAll('#itemTableBody tr').length;
        
        if (itemCount === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Tambahkan minimal 1 item untuk melanjutkan'
            });
            return false;
        }

        // ✅ Validasi jenis hanya untuk pembelian
        @if($type === 'pembelian')
            let missingJenis = false;
            let missingJenisItems = [];
            
            document.querySelectorAll('[id^="jenis-"]').forEach(input => {
                console.log('Checking jenis input:', input.id, 'value:', input.value); // Debug
                
                if (!input.value || input.value.trim() === '') {
                    missingJenis = true;
                    const itemId = input.id.replace('jenis-', '');
                    const produkSelect = document.getElementById(`produk-${itemId}`);
                    const produkName = produkSelect?.options[produkSelect.selectedIndex]?.text || `Item ${itemId}`;
                    missingJenisItems.push(produkName);
                }
            });

            if (missingJenis) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Data Tidak Lengkap',
                    html: 'Produk berikut belum memiliki jenis yang valid:<br><br>' + 
                        missingJenisItems.map(item => `• ${item}`).join('<br>'),
                    footer: 'Pilih ulang produk atau hubungi administrator'
                });
                return false;
            }
        @endif

        // Show loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
</script>
@endpush